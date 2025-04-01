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

namespace Mageplaza\GiftWrap\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\GiftWrap\Api\Data\WrapInterface;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\Price;
use Mageplaza\GiftWrap\Model\Config\Source\Status;
use Mageplaza\GiftWrap\Model\WrapManagement;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class WrapManagementTest
 * @package Mageplaza\GiftWrap\Test\Unit\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WrapManagementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var Cart|PHPUnit_Framework_MockObject_MockObject
     */
    private $cart;

    /**
     * @var CartRepositoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $cartRepository;

    /**
     * @var CartTotalRepositoryInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $cartTotalRepository;

    /**
     * @var WrapManagement
     */
    private $object;

    protected function setUp()
    {
        $this->helper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();
        $this->cart = $this->getMockBuilder(Cart::class)->disableOriginalConstructor()->getMock();
        $this->cartRepository = $this->getMockBuilder(CartRepositoryInterface::class)->getMock();
        $this->cartTotalRepository = $this->getMockBuilder(CartTotalRepositoryInterface::class)->getMock();

        $this->object = new WrapManagement(
            $this->helper,
            $this->cart,
            $this->cartRepository,
            $this->cartTotalRepository
        );
    }

    public function testUpdateGiftWrapWithAllCart()
    {
        $cartId = 199;
        $itemId = 23;
        $itemType = 'all_cart';
        $prodId = 45;
        $prodType = 'simple';

        $quote = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();

        $this->cartRepository->expects($this->once())->method('getActive')->with($cartId)->willReturn($quote);

        /** @var WrapInterface|PHPUnit_Framework_MockObject_MockObject $wrap */
        $wrap = $this->getMockBuilder(WrapInterface::class)->getMock();

        $wrapData = [
            'wrap_id' => 2,
            'name' => 'test wrap 2',
            'status' => Status::ENABLE,
            'amount' => 20,
            'description' => 'test desc 2',
            'image' => 'test image 2',
            'category' => 1,
            'sort_order' => 10,
            'price' => '$20',
            'price_type' => Price::BY_QTY,
            'created_at' => '2019-02-11 07:11:01',
            'updated_at' => '2019-02-18 08:39:30',
            'use_gift_message' => true,
            'gift_message' => 'test gift message 2',
            'all_cart' => 1
        ];

        $this->helper->method('jsonEncodeData')->with($wrap)->willReturn($wrapData);

        $item = $this->getMockBuilder(Quote\Item::class)->disableOriginalConstructor()->getMock();

        $quote->expects($this->once())->method('getAllVisibleItems')->willReturn([$item]);

        $product = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $item->expects($this->once())->method('getProduct')->willReturn($product);

        $item->method('getId')->willReturn($itemId);
        $product->method('getId')->willReturn($prodId);
        $product->method('getTypeId')->willReturn($prodType);

        $option = [
            'item_id' => $itemId,
            'product_id' => $prodId,
            'code' => Data::GIFT_WRAP_DATA,
            'value' => $wrapData,
            'product' => $product
        ];

        $item->method('addOption')->with($option)->willReturnSelf();
        $item->method('setData')->with(Data::GIFT_WRAP_DATA, $wrapData)->willReturnSelf();

        $quote->expects($this->once())->method('collectTotals');
        $this->cart->expects($this->once())->method('save');

        $this->object->updateGiftWrap($cartId, $itemType, $wrap);
    }

    public function testUpdateGiftWrapWithItemOnly()
    {
        $cartId = 199;
        $itemId = 23;
        $prodId = 45;

        $quote = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();

        $this->cartRepository->expects($this->once())->method('getActive')->with($cartId)->willReturn($quote);

        /** @var WrapInterface|PHPUnit_Framework_MockObject_MockObject $wrap */
        $wrap = $this->getMockBuilder(WrapInterface::class)->getMock();

        $wrapData = [
            'wrap_id' => 2,
            'name' => 'test wrap 2',
            'status' => Status::ENABLE,
            'amount' => 20,
            'description' => 'test desc 2',
            'image' => 'test image 2',
            'category' => 1,
            'sort_order' => 10,
            'price' => '$20',
            'price_type' => Price::BY_QTY,
            'created_at' => '2019-02-11 07:11:01',
            'updated_at' => '2019-02-18 08:39:30',
            'use_gift_message' => true,
            'gift_message' => 'test gift message 2'
        ];

        $this->helper->method('jsonEncodeData')->with($wrap)->willReturn($wrapData);

        $item = $this->getMockBuilder(Quote\Item::class)->disableOriginalConstructor()->getMock();

        $quote->expects($this->once())->method('getItemById')->with($itemId)->willReturn($item);

        $product = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $item->expects($this->once())->method('getProduct')->willReturn($product);

        $item->method('getId')->willReturn($itemId);
        $product->method('getId')->willReturn($prodId);

        $option = [
            'item_id' => $itemId,
            'product_id' => $prodId,
            'code' => Data::GIFT_WRAP_DATA,
            'value' => $wrapData,
            'product' => $product
        ];

        $item->method('addOption')->with($option)->willReturnSelf();
        $item->method('setData')->with(Data::GIFT_WRAP_DATA, $wrapData)->willReturnSelf();

        $quote->expects($this->once())->method('collectTotals');
        $this->cart->expects($this->once())->method('save');

        $this->object->updateGiftWrap($cartId, $itemId, $wrap);
    }
}
