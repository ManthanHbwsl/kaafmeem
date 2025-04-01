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

namespace Mageplaza\GiftWrap\Plugin\Model\Osc;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class CheckoutManagement
 * @package Mageplaza\GiftWrap\Plugin\Model\Osc
 */
class CheckoutManagement
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * CheckoutManagement constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * @param \Mageplaza\Osc\Model\CheckoutManagement $subject
     * @param int $cartId
     * @param int $itemId
     * @param float $itemQty
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeUpdateItemQty(
        \Mageplaza\Osc\Model\CheckoutManagement $subject,
        $cartId,
        $itemId,
        $itemQty
    ) {
        /** @var Quote $quote */
        $quote          = $this->cartRepository->getActive($cartId);
        $quoteItem      = $quote->getItemById($itemId);
        $mpGiftWrapData = Data::jsonDecode($quoteItem->getData(Data::GIFT_WRAP_DATA));

        if (!empty($mpGiftWrapData)
            && ($qtyLimit = $mpGiftWrapData[WrapDetailsInterface::QTY_LIMIT])
            && $itemQty > $quoteItem->getQty()
        ) {
            if ($itemQty > $qtyLimit) {
                throw new LocalizedException(__('We don\'t have enough wrappers to use for this item qty.'));
            }
            $wrapId      = $mpGiftWrapData[WrapDetailsInterface::WRAP_ID];
            $wrapItemQty = 0;
            /** @var Item $item */
            foreach ($quote->getAllItems() as $item) {
                $wrapItemData = Data::jsonDecode($item->getData(Data::GIFT_WRAP_DATA));
                if (isset($wrapItemData[WrapDetailsInterface::WRAP_ID])
                    && $wrapItemData[WrapDetailsInterface::WRAP_ID] === $wrapId) {
                    $wrapItemQty += $item->getQty();
                    if (($wrapItemQty + $itemQty - $quoteItem->getQty()) > $qtyLimit) {
                        throw new LocalizedException(__('We don\'t have enough wrappers to use for this item qty.'));
                    }
                }
            }
        }

        return [$cartId, $itemId, $itemQty];
    }
}
