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

namespace Mageplaza\GiftWrap\Model;

use Magento\Framework\Model\AbstractModel;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface;

/**
 * Class Wrap
 * @package Mageplaza\GiftWrap\Model
 */
class Wrap extends AbstractModel implements WrapDetailsInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Wrap::class);
    }

    /**
     * @inheritDoc
     */
    public function getWrapId()
    {
        return $this->getData(self::WRAP_ID);
    }

    /**
     * @inheritDoc
     */
    public function setWrapId($value)
    {
        return $this->setData(self::WRAP_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPriceType()
    {
        return $this->getData(self::PRICE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setPriceType($value)
    {
        return $this->setData(self::PRICE_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * @inheritDoc
     */
    public function setAmount($value)
    {
        return $this->setData(self::AMOUNT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function setDescription($value)
    {
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * @inheritDoc
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function setImage($value)
    {
        return $this->setData(self::IMAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return $this->getData(self::CATEGORY);
    }

    /**
     * @inheritDoc
     */
    public function setCategory($value)
    {
        return $this->setData(self::CATEGORY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritDoc
     */
    public function setSortOrder($value)
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($value)
    {
        return $this->setData(self::UPDATED_AT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMedia()
    {
        return $this->getData(self::MEDIA);
    }

    /**
     * @inheritDoc
     */
    public function setMedia($value)
    {
        return $this->setData(self::MEDIA, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * @inheritDoc
     */
    public function setPrice($value)
    {
        return $this->setData(self::PRICE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUseGiftMessage()
    {
        return (bool) $this->getData(self::USE_GIFT_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setUseGiftMessage($value)
    {
        return $this->setData(self::USE_GIFT_MESSAGE, (bool) $value);
    }

    /**
     * @inheritDoc
     */
    public function getGiftMessage()
    {
        return $this->getData(self::GIFT_MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setGiftMessage($value)
    {
        return $this->setData(self::GIFT_MESSAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getGiftMessageFee()
    {
        return $this->getData(self::GIFT_MESSAGE_FEE);
    }

    /**
     * @inheritDoc
     */
    public function setGiftMessageFee($value)
    {
        return $this->setData(self::GIFT_MESSAGE_FEE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getAllCart()
    {
        return (bool) $this->getData(self::ALL_CART);
    }

    /**
     * @inheritDoc
     */
    public function setAllCart($value)
    {
        return $this->setData(self::ALL_CART, (bool) $value);
    }

    /**
     * @inheritDoc
     */
    public function setWrapType($value)
    {
        return $this->setData(self::WRAP_TYPE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getWrapType()
    {
        return $this->getData(self::WRAP_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setQtyLimit($value)
    {
        return $this->setData(self::QTY_LIMIT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getQtyLimit()
    {
        return $this->getData(self::QTY_LIMIT);
    }
}
