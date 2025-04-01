<?php

namespace Humcommerce\AddCashOnDelivery\Observer\Creditmemo;

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
        $creditmemo = $observer->getData('creditmemo');

        // Update custom columns for the order
        try {
            /** @var PaymentFee $paymentFee */
            $orderFee = $this->orderFeeRepository->getByOrderId((int)$creditmemo->getOrderId());
        } catch (NoSuchEntityException $exception) {
            unset($exception);
            return;
        }
       
        $creditmemo->setData('cod_amount', $orderFee->getAmount());
        $creditmemo->setData('base_cod_amount', $orderFee->getBaseAmount());
        $creditmemo->setData('total_cod_amount', $orderFee->getAmount()+ $orderFee->getTaxAmount());
        $creditmemo->save();
    }
}

