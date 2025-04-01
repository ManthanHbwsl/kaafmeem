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

namespace Mageplaza\GiftWrap\Test\Unit\Model\Total\Creditmemo;

use Magento\Catalog\Model\Product;
use Magento\Sales\Model\Order;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\Price;
use Mageplaza\GiftWrap\Model\Config\Source\Status;
use Mageplaza\GiftWrap\Model\Total\Creditmemo\GiftWrap;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class GiftWrapTest
 * @package Mageplaza\GiftWrap\Test\Unit\Model\Total\Creditmemo
 */
class GiftWrapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var GiftWrap
     */
    private $object;

    protected function setUp()
    {
        $this->helper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        $this->object = new GiftWrap($this->helper);
    }

    public function testGetGiftWrapAmount()
    {
        /** @var Order\Creditmemo|PHPUnit_Framework_MockObject_MockObject $creditmemo */
        $creditmemo = $this->getMockBuilder(Order\Creditmemo::class)
            ->setMethods(['getOrder', 'getAllItems', 'getMpGiftWrapAllCartApplied', 'setMpGiftWrapAllCartApplied'])
            ->disableOriginalConstructor()->getMock();

        $order = $this->getMockBuilder(Order::class)
            ->setMethods(['getMpGiftWrapTax'])
            ->disableOriginalConstructor()->getMock();

        $creditmemo->expects($this->once())->method('getOrder')->willReturn($order);

        $taxRate = 8.25;
        $order->expects($this->once())->method('getMpGiftWrapTax')->willReturn($taxRate);

        $amount = $allCartAmount = $allCartMultiplier = $allCartMessageFee = $hasAllCart = 0;

        $data = [
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
            'gift_message_fee' => 2
        ];

        $qty = 2;
        $creditmemoItem = $this->getMockBuilder(Order\Creditmemo\Item::class)
            ->setMethods(['getOrderItem', 'getQty'])
            ->disableOriginalConstructor()->getMock();
        $creditmemoItem->method('getQty')->willReturn($qty);
        $item = $this->getMockBuilder(Order\Item::class)->disableOriginalConstructor()->getMock();
        $creditmemoItem->method('getOrderItem')->willReturn($item);

        $product = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $item->method('getProduct')->willReturn($product);
        $product->method('isVirtual')->willReturn(0);

        $creditmemo->expects($this->once())->method('getAllItems')->willReturn([$creditmemoItem]);

        $item->method('getData')->with(Data::GIFT_WRAP_DATA)->willReturn($data);
        $this->helper->method('jsonDecodeData')->with($data)->willReturn($data);

        $multiplier = (int)$data['price_type'] === Price::BY_QTY ? $qty : 1;

        $amount += $data['amount'] * $multiplier;

        if ($data['use_gift_message']) {
            $amount += $data['gift_message_fee'] * $multiplier;
        }

        $amount += ($allCartAmount + $allCartMessageFee) * max($allCartMultiplier, 1);
        $amount *= (1 + $taxRate / 100);

        $creditmemo->expects($this->once())->method('getMpGiftWrapAllCartApplied')->willReturn($hasAllCart);

        $creditmemo->expects($this->once())->method('setMpGiftWrapAllCartApplied')->with($hasAllCart);

        $this->assertEquals($amount, $this->object->getGiftWrapAmount($creditmemo));
    }
}
