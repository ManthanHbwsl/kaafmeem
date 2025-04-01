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

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as WrapItf;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\CategoryFactory;
use Mageplaza\GiftWrap\Model\Config\Source\GiftMessageScope;
use Mageplaza\GiftWrap\Model\Config\Source\Type;
use Mageplaza\GiftWrap\Model\WrapFactory;
use Mageplaza\GiftWrap\Model\Config\Source\WrapType;

/**
 * Class LayoutProcessor
 * @package Mageplaza\GiftWrap\Block\Checkout
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var WrapFactory
     */
    protected $wrapFactory;

    /**
     * @var Data
     */
    protected $data;

    /**
     * LayoutProcessor constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param CategoryFactory $categoryFactory
     * @param WrapFactory $wrapFactory
     * @param Data $data
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CategoryFactory $categoryFactory,
        WrapFactory $wrapFactory,
        Data $data
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->categoryFactory = $categoryFactory;
        $this->wrapFactory     = $wrapFactory;
        $this->data            = $data;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     *
     * @return array
     * @throws LocalizedException
     */
    public function process($jsLayout)
    {
        if (!$this->data->isEnabled() || $this->checkoutSession->getQuote()->isVirtual()) {
            return $jsLayout;
        }

        $wrap = $this->data->getWraps();
        if (!$wrap || $wrap === '[]') {
            return $jsLayout;
        }

        $field = [
            'component'   => 'Mageplaza_GiftWrap/js/view/checkout-item',
            'template'    => 'Mageplaza_GiftWrap/checkout-item',
            'displayArea' => 'after_details',
            'config'      => [
                'addLabel'         => $this->data->getAddLabel(),
                'changeLabel'      => $this->data->getChangeLabel(),
                'categories'       => Data::jsonDecode($this->data->getCategories()),
                'wraps'            => Data::jsonDecode($this->data->getWraps(WrapType::GIFT_WRAP_WRAP_TYPE)),
                'postcards'        => Data::jsonDecode($this->data->getWraps(WrapType::GIFT_WRAP_POST_CARD_TYPE)),
                'giftWrapIcon'     => $this->data->getGiftWrapIcon(),
                'isMessageEnabled' => $this->data->isEnabledGiftMessage(),
                'maxChar'          => $this->data->getMaxCharacters(),
                'giftMessageFee'   => $this->data->getGiftMessageFee(),
                'giftMessageVsb'   => $this->data->getGiftMessageVisible(GiftMessageScope::CHECKOUT),
                'customerNotice'   => $this->data->getNoticeForCustomer(),
                'description'      => $this->data->getDescription(),
                'isShowAllCart'    => $this->data->isShowAllCart(),
                'isShowOnProduct'  => $this->data->isShowOnProduct()
            ]
        ];

        $giftWrapType = $this->data->getGiftWrapType();
        $isOscPage    = $this->data->isOscPage();

        if ($giftWrapType !== Type::ALL) {
            $fields = &$jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']
            ['cart_items']['children'];

            if ($isOscPage) {
                $fields = &$jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']
                ['cart_items']['children']['details']['children'];

                $field['template']    = 'Mageplaza_GiftWrap/osc-item';
                $field['displayArea'] = 'after_item_details';

                $field['config']['giftMessageVsb'] = $this->data->getGiftMessageVisible(GiftMessageScope::OSC);
            }

            $fields['mpGiftWrap'] = $field;
        }

        if ($giftWrapType !== Type::ITEM) {
            $fields = &$jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children'];

            $field['component']   = 'Mageplaza_GiftWrap/js/view/all-cart';
            $field['template']    = 'Mageplaza_GiftWrap/all-cart';
            $field['displayArea'] = 'shippingAdditional';

            $field['config']['savedWrap']     = Data::jsonDecode($this->getSavedWrap(Data::GIFT_WRAP_DATA));
            $field['config']['savedPostcard'] = Data::jsonDecode($this->getSavedWrap(Data::GIFT_WRAP_POSTCARD_DATA));

            $fields['mpGiftWrap'] = $field;
        }

        if ($isOscPage) {
            $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']
            ['children']['before_grandtotal']['children']['osc_gift_wrap']['config']['componentDisabled'] = true;

            $jsLayout['components']['checkout']['children']['sidebar']['children']['place-order-information-left']
            ['children']['addition-information']['children']['gift_wrap']['config']['componentDisabled'] = true;
        }

        return $jsLayout;
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
                || (!empty($data[WrapItf::ALL_CART]) && !$this->data->isShowAllCart())
            ) {
                continue;
            }

            return $item->getData($wrapDataType);
        }

        return '{}';
    }
}
