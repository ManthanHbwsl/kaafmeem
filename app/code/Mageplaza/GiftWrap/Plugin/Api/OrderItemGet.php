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

use Magento\Sales\Api\Data\OrderItemExtension;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\WrapFactory;

/**
 * Class OrderItemGet
 * @package Mageplaza\GiftWrap\Plugin\Api
 */
class OrderItemGet
{
    /**
     * @var WrapFactory
     */
    private $wrapDetailsFactory;

    /**
     * @var OrderItemExtensionFactory
     */
    private $itemExtensionFactory;

    /**
     * OrderItemGet constructor.
     *
     * @param WrapFactory $wrapDetailsFactory
     * @param OrderItemExtensionFactory $itemExtensionFactory
     */
    public function __construct(
        WrapFactory $wrapDetailsFactory,
        OrderItemExtensionFactory $itemExtensionFactory
    ) {
        $this->wrapDetailsFactory   = $wrapDetailsFactory;
        $this->itemExtensionFactory = $itemExtensionFactory;
    }

    /**
     * @param OrderItemRepositoryInterface $subject
     * @param Collection $result
     *
     * @return Collection
     */
    public function afterGetList(OrderItemRepositoryInterface $subject, Collection $result)
    {
        foreach ($result->getItems() as $item) {
            $this->afterGet($subject, $item);
        }

        return $result;
    }

    /**
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface $result
     *
     * @return OrderItemInterface
     */
    public function afterGet(OrderItemRepositoryInterface $subject, OrderItemInterface $result)
    {
        return $this->getOrderItemData($result);
    }

    /**
     * @param OrderItemInterface|Item $item
     *
     * @return OrderItemInterface
     */
    private function getOrderItemData($item)
    {
        $extensionAttributes = $item->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getMpGiftWrap()) {
            return $item;
        }

        $wrapData     = $item->getData(Data::GIFT_WRAP_DATA);
        $postcardData = $item->getData(Data::GIFT_WRAP_POSTCARD_DATA);
        if (empty($wrapData) && empty($postcardData)) {
            return $item;
        }

        /** @var OrderItemExtension $orderItemExtension */
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

        return $item;
    }
}
