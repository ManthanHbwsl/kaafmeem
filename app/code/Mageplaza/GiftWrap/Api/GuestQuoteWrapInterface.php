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

namespace Mageplaza\GiftWrap\Api;

/**
 * Interface GuestQuoteWrapInterface
 * @package Mageplaza\GiftWrap\Api
 */
interface GuestQuoteWrapInterface
{
    /**
     * @param string $cartId
     * @param string $itemId
     * @param \Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface $wrap
     * @param \Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface $postcard
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateGiftWrap($cartId, $itemId, $wrap, $postcard);
}
