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

namespace Mageplaza\GiftWrap\Block\Checkout;

use Magento\Framework\View\Element\Template;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\Type;

/**
 * Class ItemOnly
 * @package Mageplaza\GiftWrap\Block\Checkout\Cart
 */
class ItemOnly extends Template
{
    const ITEM = 'item';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * ItemOnly constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->getData(static::ITEM);
    }

    /**
     * @param Item $item
     *
     * @return $this
     */
    public function setItem($item)
    {
        $this->setData(static::ITEM, $item);

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->helper->isEnabled()
            || $this->helper->getGiftWrapType() === Type::ALL
            || !in_array($this->getItem()->getProduct()->getTypeId(), Data::ALLOWED_TYPE, true);
    }

    /**
     * @return Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @param string $wrapDataType
     *
     * @return mixed|string
     */
    public function getSavedWrap($wrapDataType)
    {
        $giftWrapData    = $this->getItem()->getData($wrapDataType);
        $giftWrapDataArr = Data::jsonDecode($giftWrapData);

        if (!$giftWrapData
            || (!empty($giftWrapDataArr) && !empty($giftWrapDataArr['all_cart']) && !$this->helper->isShowAllCart())
            || (!empty($giftWrapDataArr) && empty($giftWrapDataArr['all_cart']) && !$this->helper->isShowOnProduct())
        ) {
            return '{}';
        }

        return $giftWrapData;
    }
}
