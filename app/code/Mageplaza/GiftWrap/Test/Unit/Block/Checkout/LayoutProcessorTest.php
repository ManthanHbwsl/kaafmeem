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

namespace Mageplaza\GiftWrap\Test\Unit\Block\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Mageplaza\GiftWrap\Block\Checkout\LayoutProcessor;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\CategoryFactory;
use Mageplaza\GiftWrap\Model\Config\Source\Type;
use Mageplaza\GiftWrap\Model\WrapFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class LayoutProcessorTest
 * @package Mageplaza\GiftWrap\Test\Unit\Block\Checkout
 */
class LayoutProcessorTest extends TestCase
{
    /**
     * @var CheckoutSession|PHPUnit_Framework_MockObject_MockObject
     */
    private $checkoutSession;

    /**
     * @var CategoryFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryFactory;

    /**
     * @var WrapFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $wrapFactory;

    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var LayoutProcessor
     */
    private $object;

    protected function setUp()
    {
        $this->checkoutSession = $this->getMockBuilder(CheckoutSession::class)->disableOriginalConstructor()->getMock();
        $this->categoryFactory = $this->getMockBuilder(CategoryFactory::class)->disableOriginalConstructor()->getMock();
        $this->wrapFactory = $this->getMockBuilder(WrapFactory::class)->disableOriginalConstructor()->getMock();
        $this->helper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        $this->object = new LayoutProcessor(
            $this->checkoutSession,
            $this->categoryFactory,
            $this->wrapFactory,
            $this->helper
        );
    }

    /**
     * @throws LocalizedException
     */
    public function testProcessWithGiftWrapTypeBothAndInOscPage()
    {
        $jsLayout = [
            'components' => [
                'checkout' => [
                    'children' => [
                        'steps' => [
                            'children' => [
                                'shipping-step' => [
                                    'children' => [
                                        'shippingAddress' => [
                                            'children' => []
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'sidebar' => [
                            'children' => [
                                'summary' => [
                                    'children' => [
                                        'cart_items' => [
                                            'children' => [
                                                'details' => [
                                                    'children' => []
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->helper->expects($this->once())->method('isEnabled')->willReturn(1);

        $giftWrapIcon = '/path/to/icon';
        $isEnableGM = 1;
        $maxChars = 120;
        $giftMessageFee = 2;
        $customerNotice = 'notice for customer';
        $giftWrapType = Type::BOTH;
        $isOscPage = 1;
        $addLabel = 'add label';
        $changeLabel = 'change label';
        $giftMessageVsb = 1;

        $this->helper->expects($this->once())->method('getGiftWrapIcon')->willReturn($giftWrapIcon);
        $this->helper->expects($this->once())->method('isEnabledGiftMessage')->willReturn($isEnableGM);
        $this->helper->expects($this->once())->method('getMaxCharacters')->willReturn($maxChars);
        $this->helper->expects($this->once())->method('getGiftMessageFee')->willReturn($giftMessageFee);
        $this->helper->expects($this->once())->method('getNoticeForCustomer')->willReturn($customerNotice);
        $this->helper->expects($this->once())->method('getGiftWrapType')->willReturn($giftWrapType);
        $this->helper->expects($this->once())->method('isOscPage')->willReturn($isOscPage);
        $this->helper->expects($this->once())->method('getAddLabel')->willReturn($addLabel);
        $this->helper->expects($this->once())->method('getChangeLabel')->willReturn($changeLabel);
        $this->helper->method('getGiftMessageVisible')->willReturn($giftMessageVsb);

        $quote = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();
        $this->checkoutSession->method('getQuote')->willReturn($quote);
        $quote->expects($this->once())->method('isVirtual')->willReturn(0);
        $quote->expects($this->once())->method('getAllVisibleItems')->willReturn([]);

        $field = [
            'component' => 'Mageplaza_GiftWrap/js/view/checkout-item',
            'template' => 'Mageplaza_GiftWrap/checkout-item',
            'displayArea' => 'after_details',
            'config' => [
                'addLabel' => $addLabel,
                'changeLabel' => $changeLabel,
                'categories' => [],
                'wraps' => [],
                'giftWrapIcon' => $giftWrapIcon,
                'isMessageEnabled' => $isEnableGM,
                'maxChar' => $maxChars,
                'giftMessageFee' => $giftMessageFee,
                'giftMessageVsb' => $giftMessageVsb,
                'customerNotice' => $customerNotice
            ]
        ];

        $field['template'] = 'Mageplaza_GiftWrap/osc-item';
        $field['displayArea'] = 'after_item_details';

        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']
        ['cart_items']['children']['details']['children']['mpGiftWrap'] = $field;

        $field['component'] = 'Mageplaza_GiftWrap/js/view/all-cart';
        $field['template'] = 'Mageplaza_GiftWrap/all-cart';
        $field['displayArea'] = 'shippingAdditional';

        $field['config']['savedWrap'] = [];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['mpGiftWrap'] = $field;

        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['totals']
        ['children']['before_grandtotal']['children']['osc_gift_wrap']['config']['componentDisabled'] = true;

        $jsLayout['components']['checkout']['children']['sidebar']['children']['place-order-information-left']
        ['children']['addition-information']['children']['gift_wrap']['config']['componentDisabled'] = true;

        $this->assertEquals($jsLayout, $this->object->process($jsLayout));
    }
}
