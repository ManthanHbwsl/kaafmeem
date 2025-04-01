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

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as WrapItf;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\Type;

/**
 * Class AllCart
 * @package Mageplaza\GiftWrap\Block\Checkout\Cart
 */
class AllCart extends Template
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * AllCart constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param CheckoutSession $checkoutSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        $this->helper          = $helper;
        $this->checkoutSession = $checkoutSession;

        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->helper->isEnabled() || (int) $this->helper->getGiftWrapType() === Type::ITEM;
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
     * @return mixed|string|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getSavedWrap($wrapDataType)
    {
        foreach ($this->checkoutSession->getQuote()->getAllVisibleItems() as $item) {
            $data = Data::jsonDecode($item->getData($wrapDataType));

            if (empty($data[WrapItf::ALL_CART])
                || (!empty($data[WrapItf::ALL_CART]) && !$this->helper->isShowAllCart())) {
                continue;
            }

            return $item->getData($wrapDataType);
        }

        return '{}';
    }
}
