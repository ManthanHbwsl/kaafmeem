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

namespace Mageplaza\GiftWrap\Model\Api\Data;

use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftWrap\Api\Data\WrapItemInterface;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class WrapItem
 * @package Mageplaza\GiftWrap\Model\Api\Data
 */
class WrapItem extends Item implements WrapItemInterface
{
    /**
     * @inheritDoc
     */
    public function getMpGiftWrapData()
    {
        return $this->getData(Data::GIFT_WRAP_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setMpGiftWrapData($value)
    {
        return $this->setData(Data::GIFT_WRAP_DATA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMpGiftWrapPostcardData()
    {
        return $this->getData(Data::GIFT_WRAP_POSTCARD_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setMpGiftWrapPostcardData($value)
    {
        return $this->setData(Data::GIFT_WRAP_POSTCARD_DATA, $value);
    }
}
