<?php
/**
 * @category   Webiators
 * @package    Webiators_AutoInvoiceAndShipment
 * @author     Webiators Team
 * @copyright  Copyright (c) Webiators Technologies Private Limited. ( https://webiators.com ).
 */

namespace Webiators\AutoInvoiceAndShipment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use \Psr\Log\LoggerInterface;

class Data extends AbstractHelper
{
    CONST EMAIL_INVOICE = 'webiator_autoinvoice/general/emailInvoice';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     *
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;

    /**
     *
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $order;

    /**
     *
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $transactionFactory;
    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\InvoiceSender
     */
    protected $invoiceSender;
    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    protected $shipmentNotifier;

     /**
     * ShipmentSender
     *
     * @var ShipmentSender $shipmentSender
     */
    protected $shipmentSender;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender
     * @param \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
     * @param \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory $invoiceCollectionFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $order,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->invoiceService = $invoiceService;
        $this->shipmentFactory = $shipmentFactory;
        $this->convertOrder = $convertOrder;
        $this->order = $order;
        $this->transactionFactory = $transactionFactory;
        $this->invoiceSender = $invoiceSender;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->shipmentSender = $shipmentSender;
    }

    public function isAutoInvoiceShipmentEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('webiator_autoinvoice/general/enable_autoinvoiceandshipment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isAutoInvoiceEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('webiator_autoinvoice/general/enable_autoinvoice', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isAutoShipmentEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue('webiator_autoinvoice/general/enable_autoshipment', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getPaymentMethods($storeId = null)
    {
        return $this->scopeConfig->getValue('webiator_autoinvoice/general/adminpayments', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isEnabledInvoiceEmail()
    {
        return ($this->scopeConfig->getValue(self::EMAIL_INVOICE)) ? true:false ;
    }

    /**
     * Get Order Data
     * @param $orderId
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getOrderByOrderId($orderId)
    {
        $order = $this->order->get($orderId);
        return $order;
    }

     /**
     * @return LoggerInterface
     */
    public function returnLogger()
    {
        return $this->_logger;
    }

    public function createInvoice($order)
    {
        try {
            $invoices = $this->invoiceCollectionFactory->create()
            ->addAttributeToFilter('order_id', array('eq' => $order->getId()));
            $invoices->getSelect()->limit(1);
            if ((int)$invoices->count() !== 0) {
                return null;
            }
            if(!$order->canInvoice()) {
                $order->addStatusHistoryComment('Order cannot be invoiced.', false);
                $order->save();
                return null;
            }
            if(!$order->getState() == 'new') {
                return null;
            }
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);
            $transactionSave = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
            $transactionSave->save();
            if($this->isEnabledInvoiceEmail())
            {
                $this->invoiceSender->send($invoice);
                $order->addStatusHistoryComment('Automatically Invoice Created by Webiators.', false)->setIsCustomerNotified(true)->save();
            }

        } catch (\Exception $e) {
            $order->addStatusHistoryComment('Exception message: '.$e->getMessage(), false);
            $order->save();
            return null;
        }
        return $transactionSave;
    }

    public function createShipment($order)
    {
        try {

            $shipment = $this->convertOrder->toShipment($order);
            foreach ($order->getAllItems() as $orderItem) {
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $qtyShipped = $orderItem->getQtyToShip();
                $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                $shipment->addItem($shipmentItem);
            }
            $shipment->register();
            $shipment->getOrder()->setIsInProcess(true);
            $shipment->save();
            $shipment->getOrder()->save();
            if ($shipment->save()) {
                $order->addStatusHistoryComment('Automatically Shipment Created by Webiators.', false)->save();
            }

        } catch (\Exception $e) {
            $order->addStatusHistoryComment('Exception message: '.$e->getMessage(), false);
            $order->save();
            return null;
        }
    }

}