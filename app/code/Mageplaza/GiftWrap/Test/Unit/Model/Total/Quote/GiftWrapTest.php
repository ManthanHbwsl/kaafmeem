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

namespace Mageplaza\GiftWrap\Test\Unit\Model\Total\Quote;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote;
use Magento\Tax\Model\Calculation;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\Price;
use Mageplaza\GiftWrap\Model\Config\Source\Status;
use Mageplaza\GiftWrap\Model\Total\Quote\GiftWrap;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class GiftWrapTest
 * @package Mageplaza\GiftWrap\Test\Unit\Model\Total\Quote
 */
class GiftWrapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Calculation|PHPUnit_Framework_MockObject_MockObject
     */
    private $calculationTool;

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
        $this->calculationTool = $this->getMockBuilder(Calculation::class)->disableOriginalConstructor()->getMock();
        $this->helper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        $this->object = new GiftWrap($this->calculationTool, $this->helper);
    }

    public function testGetGiftWrapAmount()
    {
        /** @var Quote|PHPUnit_Framework_MockObject_MockObject $quote */
        $quote = $this->getMockBuilder(Quote::class)->disableOriginalConstructor()->getMock();

        $addressObject = $this->getMockBuilder(DataObject::class)
            ->setMethods(['setProductClassId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->calculationTool->expects($this->once())->method('getRateRequest')->willReturn($addressObject);

        $taxClass = 2;

        $this->helper->expects($this->once())->method('getTaxClass')->willReturn($taxClass);
        $addressObject->expects($this->once())->method('setProductClassId')->with($taxClass);

        $taxRate = 8.25;
        $this->calculationTool->expects($this->once())->method('getRate')->with($addressObject)->willReturn($taxRate);

        $amount = $allCartAmount = $allCartMultiplier = $allCartMessageFee = 0;

        $giftMessageFee = 2;
        $this->helper->expects($this->once())->method('getGiftMessageFee')->with(false)->willReturn($giftMessageFee);

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
            'gift_message' => 'test gift message 2'
        ];

        $qty = 2;
        $quoteItem = $this->getMockBuilder(Quote\Item::class)->disableOriginalConstructor()->getMock();
        $quoteItem->method('getQty')->willReturn($qty);

        $product = $this->getMockBuilder(Product::class)->disableOriginalConstructor()->getMock();
        $quoteItem->method('getProduct')->willReturn($product);
        $product->method('isVirtual')->willReturn(0);

        $quote->expects($this->once())->method('getAllVisibleItems')->willReturn([$quoteItem]);

        $quoteItem->method('getData')->with(Data::GIFT_WRAP_DATA)->willReturn($data);
        $this->helper->method('jsonDecodeData')->with($data)->willReturn($data);

        $multiplier = (int)$data['price_type'] === Price::BY_QTY ? $qty : 1;

        $amount += $data['amount'] * $multiplier;

        if ($data['use_gift_message']) {
            $amount += $giftMessageFee * $multiplier;
        }

        $amount += ($allCartAmount + $allCartMessageFee) * max($allCartMultiplier, 1);
        $amount *= (1 + $taxRate / 100);

        $this->assertEquals([$amount, $taxRate], $this->object->getGiftWrapAmount($quote));
    }
}
