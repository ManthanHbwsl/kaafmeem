<?php
/**
 * @category   Webiators
 * @package    Webiators_AutoInvoiceAndShipment
 * @author     Webiators Team
 * @copyright  Copyright (c) Webiators Technologies. ( https://webiators.com ).
 */
namespace Webiators\AutoInvoiceAndShipment\Observer;
use Magento\Framework\Event\Observer;

class CreateAutoInvoice implements \Magento\Framework\Event\ObserverInterface
{
 	/**
     *
     * @var \Webiators\AutoInvoiceAndShipment\Helper\Data
     */
    protected $helper;

    /**
     * @param \Webiators\AutoInvoiceAndShipment\Helper\Data $helper
     */
    public function __construct(
        \Webiators\AutoInvoiceAndShipment\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {

          try {
            if ($this->helper->isAutoInvoiceShipmentEnabled()) {
                 $order = $observer->getEvent()->getOrder();
                if ($order !== NULL ) {
                    $admin_pay_method = $this->helper->getPaymentMethods();
                    $admin_payment_method = explode(",",$admin_pay_method);
                    $payment_method_code = $order->getPayment()->getMethodInstance()->getCode();

                    if (!$order->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The order is no longer exists.'));
                    }

                    // Create invoice and shipment
                    if (!in_array($payment_method_code, $admin_payment_method)) {
                        return;
                    }

                    if($this->helper->isAutoInvoiceEnabled()) {
                        $invoice = $this->helper->createInvoice($order);
                    }
                    if($this->helper->isAutoShipmentEnabled()) {
                        $shipment = $this->helper->createShipment($order);
                    }
                }

            }
        } catch (\Exception $e) {
            $this->helper->returnLogger()->error($e->getMessage());
        }

    }
}