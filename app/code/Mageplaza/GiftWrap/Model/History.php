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
use Mageplaza\GiftWrap\Api\Data\HistoryInterface;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class History
 * @package Mageplaza\GiftWrap\Model
 */
class History extends AbstractModel implements HistoryInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\History::class);
    }

    /**
     * @inheritDoc
     */
    public function getHistoryId()
    {
        return $this->getData(self::HISTORY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setHistoryId($value)
    {
        return $this->setData(self::HISTORY_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOrderId($value)
    {
        return $this->setData(self::ORDER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getIncrId()
    {
        return $this->getData(self::INCR_ID);
    }

    /**
     * @inheritDoc
     */
    public function setIncrId($value)
    {
        return $this->setData(self::INCR_ID, $value);
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
    public function getWrap()
    {
        return $this->getData(self::WRAP);
    }

    /**
     * @inheritDoc
     */
    public function setWrap($value)
    {
        return $this->setData(self::WRAP, $value);
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
    public function getProductList()
    {
        $data = Data::jsonDecode($this->getData(self::PRODUCT_LIST));
        $result = '';

        foreach ($data as $datum) {
            if ($result) {
                $result .= ', ';
            }
            $result .= $datum['name'] . ' x ' . $datum['qty'];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setProductList($value)
    {
        return $this->setData(self::PRODUCT_LIST, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($value)
    {
        return $this->setData(self::MESSAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getOrderDate()
    {
        return $this->getData(self::ORDER_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setOrderDate($value)
    {
        return $this->setData(self::ORDER_DATE, $value);
    }
}
