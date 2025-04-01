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
 * Interface WrapItemInterface
 * @api
 */
interface WrapItemInterface extends \Magento\Quote\Api\Data\CartItemInterface
{
    /**
     * @return \Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface
     */
    public function getMpGiftWrapData();

    /**
     * @param \Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface $value
     *
     * @return $this
     */
    public function setMpGiftWrapData($value);

    /**
     * @return \Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface
     */
    public function getMpGiftWrapPostcardData();

    /**
     * @param \Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface $value
     *
     * @return $this
     */
    public function setMpGiftWrapPostcardData($value);
}
