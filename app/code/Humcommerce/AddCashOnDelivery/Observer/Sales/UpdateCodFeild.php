<?php

namespace Humcommerce\AddCashOnDelivery\Observer\Sales;

use Amasty\CashOnDelivery\Api\PaymentFeeRepositoryInterface;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\NoSuchEntityException;


class UpdateCodFeild implements ObserverInterface
{
    /**
     * @var PaymentFeeRepositoryInterface
     */
    private $quoteFeeRepository;

    /**
     * @param PaymentFeeRepositoryInterface $quoteFeeRepository
     */
    public function __construct(
        PaymentFeeRepositoryInterface $quoteFeeRepository
    ) {
        $this->quoteFeeRepository = $quoteFeeRepository;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getData('order');

        // Update custom columns for the order
        try {
            /** @var PaymentFee $paymentFee */
            $quoteFee = $this->quoteFeeRepository->getByQuoteId((int)$order->getQuoteId());
        } catch (NoSuchEntityException $exception) {
            unset($exception);
            return;
        }

        $order->setData('cod_amount', $quoteFee->getAmount());
        $order->setData('base_cod_amount', $quoteFee->getBaseAmount());
        $order->setData('total_cod_amount', $quoteFee->getAmount()+ $quoteFee->getTaxAmount());
        $order->save();
    }
}

