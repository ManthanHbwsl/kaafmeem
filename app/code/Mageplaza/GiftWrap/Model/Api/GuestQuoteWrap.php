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

namespace Mageplaza\GiftWrap\Model\Api;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Mageplaza\GiftWrap\Api\GuestQuoteWrapInterface;
use Mageplaza\GiftWrap\Api\QuoteWrapInterface;

/**
 * Class GuestQuoteWrap
 * @package Mageplaza\GiftWrap\Model\Api
 */
class GuestQuoteWrap implements GuestQuoteWrapInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @type QuoteWrapInterface
     */
    private $wrapManagement;

    /**
     * GuestQuoteWrap constructor.
     *
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param QuoteWrapInterface $wrapManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteWrapInterface $wrapManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->wrapManagement     = $wrapManagement;
    }

    /**
     * {@inheritDoc}
     */
    public function updateGiftWrap($cartId, $itemId, $wrap, $postcard)
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->wrapManagement->updateGiftWrap($quoteIdMask->getQuoteId(), $itemId, $wrap, $postcard);
    }
}
