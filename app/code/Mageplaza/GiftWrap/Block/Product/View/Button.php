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

namespace Mageplaza\GiftWrap\Block\Product\View;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\ItemFactory;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class Button
 * @package Mageplaza\GiftWrap\Block\Product\View
 */
class Button extends Template
{
    const ITEM = 'item';

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Button constructor.
     *
     * @param Template\Context $context
     * @param Registry $registry
     * @param Data $helper
     * @param ItemFactory $itemFactory
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        Data $helper,
        ItemFactory $itemFactory,
        RequestInterface $request,
        array $data = []
    ) {
        $this->registry    = $registry;
        $this->helper      = $helper;
        $this->itemFactory = $itemFactory;
        $this->request     = $request;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->helper->isEnabled()
            || !$this->helper->isShowOnProduct()
            || !in_array($this->getProduct()->getTypeId(), Data::ALLOWED_TYPE, true);
    }

    /**
     * @return Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->registry->registry('product');
        }

        return $this->_product;
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
     * @return string
     */
    public function getSavedWrap($wrapDataType)
    {
        $item = $this->itemFactory->create()->load($this->request->getParam('id'));

        if (!$item || !$item->getId()) {
            return '{}';
        }
        $giftWrapData    = $item->getData($wrapDataType);
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
