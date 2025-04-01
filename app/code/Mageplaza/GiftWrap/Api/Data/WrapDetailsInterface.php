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

namespace Mageplaza\GiftWrap\Api\Data;

/**
 * Interface WrapDetailsInterface
 * @api
 */
interface WrapDetailsInterface extends WrapInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const PRICE            = 'price';
    const USE_GIFT_MESSAGE = 'use_gift_message';
    const GIFT_MESSAGE     = 'gift_message';
    const GIFT_MESSAGE_FEE = 'gift_message_fee';
    const ALL_CART         = 'all_cart';

    /**
     * @return string
     */
    public function getPrice();

    /**
     * @param string $price
     *
     * @return $this
     */
    public function setPrice($price);

    /**
     * @return bool
     */
    public function getUseGiftMessage();

    /**
     * @param bool $useGiftMessage
     *
     * @return $this
     */
    public function setUseGiftMessage($useGiftMessage);

    /**
     * @return string
     */
    public function getGiftMessage();

    /**
     * @param string $giftMessage
     *
     * @return $this
     */
    public function setGiftMessage($giftMessage);

    /**
     * @return float
     */
    public function getGiftMessageFee();

    /**
     * @param float $giftMessageFee
     *
     * @return $this
     */
    public function setGiftMessageFee($giftMessageFee);

    /**
     * @return bool
     */
    public function getAllCart();

    /**
     * @param bool $allCart
     *
     * @return $this
     */
    public function setAllCart($allCart);
}
