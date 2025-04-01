<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftWrap
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftWrap\Plugin\Api;

use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemExtension;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\WrapFactory;

/**
 * Class OrderGet
 * @package Mageplaza\GiftWrap\Plugin\Api
 */
class OrderGet
{
    /**
     * @var WrapFactory
     */
    protected $wrapDetailsFactory;

    /**
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * @var OrderItemExtensionFactory
     */
    protected $itemExtensionFactory;

    /**
     * OrderGet constructor.
     *
     * @param WrapFactory $wrapDetailsFactory
     * @param OrderExtensionFactory $extensionFactory
     * @param OrderItemExtensionFactory $itemExtensionFactory
     */
    public function __construct(
        WrapFactory $wrapDetailsFactory,
        OrderExtensionFactory $extensionFactory,
        OrderItemExtensionFactory $itemExtensionFactory
    ) {
        $this->wrapDetailsFactory   = $wrapDetailsFactory;
        $this->extensionFactory     = $extensionFactory;
        $this->itemExtensionFactory = $itemExtensionFactory;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param Collection $resultOrder
     *
     * @return Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        Collection $resultOrder
    ) {
        foreach ($resultOrder->getItems() as $order) {
            $this->afterGet($subject, $order);
        }

        return $resultOrder;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $resultOrder)
    {
        $resultOrder = $this->getOrderData($resultOrder);
        $resultOrder = $this->getOrderItemData($resultOrder);

        return $resultOrder;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    protected function getOrderData(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getMpGiftWrapAmount()
            && $extensionAttributes->getMpGiftWrapPostcardAmount()) {
            return $order;
        }

        /** @var OrderExtension $orderExtension */
        $orderExtension = $extensionAttributes ?: $this->extensionFactory->create();
        if (!$extensionAttributes->getMpGiftWrapAmount()) {
            $orderExtension->setMpGiftWrapAmount($order->getMpGiftWrapAmount());
            $orderExtension->setMpGiftWrapBaseAmount($order->getMpGiftWrapBaseAmount());
        }
        if (!$extensionAttributes->getMpGiftWrapPostcardAmount()) {
            $orderExtension->setMpGiftWrapPostcardAmount($order->getMpGiftWrapPostcardAmount());
            $orderExtension->setMpGiftWrapPostcardBaseAmount($order->getMpGiftWrapPostcardBaseAmount());
        }
        $order->setExtensionAttributes($orderExtension);

        return $order;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    protected function getOrderItemData(OrderInterface $order)
    {
        /** @var Item $item */
        foreach ($order->getItems() as $item) {
            $extensionAttributes = $item->getExtensionAttributes();
            if ($extensionAttributes && $extensionAttributes->getMpGiftWrap()
                && $extensionAttributes->getMpGiftWrapPostcard()) {
                continue;
            }

            $wrapData     = $item->getData(Data::GIFT_WRAP_DATA);
            $postcardData = $item->getData(Data::GIFT_WRAP_POSTCARD_DATA);

            $orderItemExtension = $extensionAttributes ?: $this->itemExtensionFactory->create();
            if (!empty($wrapData)) {
                $orderItemExtension->setMpGiftWrap(
                    $this->wrapDetailsFactory->create()->setData(Data::jsonDecode($wrapData))
                );
            }

            if (!empty($postcardData)) {
                $orderItemExtension->setMpGiftWrapPostcard(
                    $this->wrapDetailsFactory->create()->setData(Data::jsonDecode($postcardData))
                );
            }

            $item->setExtensionAttributes($orderItemExtension);
        }

        return $order;
    }
}
