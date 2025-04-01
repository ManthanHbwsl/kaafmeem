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

namespace Mageplaza\GiftWrap\Test\Unit\Observer;

use Exception;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Category;
use Mageplaza\GiftWrap\Model\CategoryFactory;
use Mageplaza\GiftWrap\Model\Config\Source\Price;
use Mageplaza\GiftWrap\Model\Config\Source\Status;
use Mageplaza\GiftWrap\Model\History;
use Mageplaza\GiftWrap\Model\HistoryFactory;
use Mageplaza\GiftWrap\Observer\QuoteSubmitSuccess;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class QuoteSubmitSuccessTest
 * @package Mageplaza\GiftWrap\Test\Unit\Observer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QuoteSubmitSuccessTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CategoryFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryFactory;

    /**
     * @var HistoryFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $historyFactory;

    /**
     * @var Data|PHPUnit_Framework_MockObject_MockObject
     */
    private $helper;

    /**
     * @var QuoteSubmitSuccess
     */
    private $object;

    protected function setUp()
    {
        $this->categoryFactory = $this->getMockBuilder(CategoryFactory::class)->disableOriginalConstructor()->getMock();
        $this->historyFactory = $this->getMockBuilder(HistoryFactory::class)->disableOriginalConstructor()->getMock();
        $this->helper = $this->getMockBuilder(Data::class)->disableOriginalConstructor()->getMock();

        $this->object = new QuoteSubmitSuccess($this->categoryFactory, $this->historyFactory, $this->helper);
    }

    /**
     * @throws Exception
     */
    public function testExecute()
    {
        /** @var Observer|PHPUnit_Framework_MockObject_MockObject $observer */
        $observer = $this->getMockBuilder(Observer::class)->disableOriginalConstructor()->getMock();
        $event = $this->getMockBuilder(Event::class)
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();
        $order = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();

        $observer->method('getEvent')->willReturn($event);
        $event->method('getOrder')->willReturn($order);

        $storeId = 1;
        $order->expects($this->once())->method('getStoreId')->willReturn($storeId);
        $this->helper->expects($this->once())->method('isEnabled')->with($storeId)->willReturn(1);

        $orderId = 49;
        $incrId = '00000049';

        $order->expects($this->once())->method('getId')->willReturn($orderId);
        $order->expects($this->once())->method('getIncrementId')->willReturn($incrId);

        $orderItem = $this->getMockBuilder(Order\Item::class)->disableOriginalConstructor()->getMock();

        $order->expects($this->once())->method('getAllItems')->willReturn([$orderItem]);

        $itemId = 12;
        $itemName = 'test item item';
        $itemQty = 3;
        $orderItem->method('getId')->willReturn($itemId);
        $orderItem->method('getName')->willReturn($itemName);
        $orderItem->method('getQtyOrdered')->willReturn($itemQty);

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
            'category_name' => 'test cate 1'
        ];

        $orderItem->method('getData')->with(Data::GIFT_WRAP_DATA)->willReturn($data);
        $this->helper->method('jsonDecodeData')->with($data)->willReturn($data);

        $identifier = empty($data[WrapDetailsInterface::ALL_CART]) ? $itemId . '_item' : $data['wrap_id'] . '_cart';

        $category = $this->getMockBuilder(Category::class)
            ->setMethods(['load', 'getName'])
            ->disableOriginalConstructor()->getMock();
        $this->categoryFactory->method('create')->willReturn($category);
        $category->method('load')->with($data['category'])->willReturnSelf();
        $category->method('getName')->willReturn($data['category_name']);

        $wraps[$identifier]['wrap_id'] = $data['wrap_id'];
        $wraps[$identifier]['wrap'] = $data['name'];
        $wraps[$identifier]['category'] = $data['category_name'];
        $wraps[$identifier]['image'] = $data['image'];
        $wraps[$identifier]['order_id'] = $orderId;
        $wraps[$identifier]['incr_id'] = $incrId;
        $wraps[$identifier]['message'] = $data['use_gift_message'] ? $data['gift_message'] : '';

        if (empty($wraps[$identifier]['product_list'])) {
            $wraps[$identifier]['product_list'] = [];
        }

        $wraps[$identifier]['product_list'][] = [
            'id' => $itemId,
            'name' => $itemName,
            'qty' => $itemQty
        ];

        foreach ($wraps as $wrap) {
            $history = $this->getMockBuilder(History::class)->disableOriginalConstructor()->getMock();

            $this->historyFactory->method('create')->willReturn($history);

            $this->helper->method('jsonEncodeData')->with($wrap['product_list'])->willReturn($wrap['product_list']);

            $history->expects($this->atLeastOnce())->method('setData')->with($wrap)->willReturnSelf();
            $history->expects($this->atLeastOnce())->method('save')->willReturnSelf();
        }

        $this->object->execute($observer);
    }
}
