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

namespace Mageplaza\GiftWrap\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface;

/**
 * Class UpdateItems
 * @package Mageplaza\GiftWrap\Observer
 */
class UpdateItems implements ObserverInterface
{
    /**
     * @param Observer $observer
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $info  = $observer->getEvent()->getData('info');
        $cart  = $observer->getEvent()->getData('cart');
        $quote = $cart->getQuote();

        $wrapItemQty = [];
        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            $wrapItemData = Data::jsonDecode($item->getData(Data::GIFT_WRAP_DATA));
            if (empty($wrapItemData) || !$wrapItemData[WrapDetailsInterface::QTY_LIMIT]
                || $info[$item->getId()]['qty'] <= $item->getQty()) {
                continue;
            }
            $wrapId = $wrapItemData[WrapDetailsInterface::WRAP_ID];
            if (!isset($wrapItemQty[$wrapId])) {
                $wrapItemQty[$wrapId] = 0;
            }
            $wrapItemQty[$wrapId] += $info[$item->getId()]['qty'];
            if ($wrapItemQty[$wrapId] > $wrapItemData[WrapDetailsInterface::QTY_LIMIT]) {
                throw new LocalizedException(__('We don\'t have enough wrappers to use for this item qty.'));
            }
        }
    }
}
