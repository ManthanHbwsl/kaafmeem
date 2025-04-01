<?php

namespace Humcommerce\AddCashOnDelivery\Observer\Invoice;

use Amasty\CashOnDelivery\Api\OrderPaymentFeeRepositoryInterface;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;


class UpdateCodFeild implements ObserverInterface
{
    /**
     * @var OrderPaymentFeeRepositoryInterface
     */
    private $orderFeeRepository;

    /**
     * @param OrderPaymentFeeRepositoryInterface $orderFeeRepository
     */
    public function __construct(
        OrderPaymentFeeRepositoryInterface $orderFeeRepository
    ) {
        $this->orderFeeRepository = $orderFeeRepository;
    }

    public function execute(Observer $observer)
    {
        $invoice = $observer->getData('invoice');

        // Update custom columns for the order
        try {
            /** @var PaymentFee $paymentFee */
            $orderFee = $this->orderFeeRepository->getByOrderId((int)$invoice->getOrderId());
        } catch (NoSuchEntityException $exception) {
            unset($exception);
            return;
        }
        
        $invoice->setData('cod_amount', $orderFee->getAmount());
        $invoice->setData('base_cod_amount', $orderFee->getBaseAmount());
        $invoice->setData('total_cod_amount', $orderFee->getAmount()+ $orderFee->getTaxAmount());
        $invoice->save();
    }
}
