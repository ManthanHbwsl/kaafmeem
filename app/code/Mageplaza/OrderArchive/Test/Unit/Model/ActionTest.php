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
 * @category  Mageplaza
 * @package   Mageplaza_OrderArchive
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderArchive\Test\Unit\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\OrderFactory;
use Mageplaza\OrderArchive\Helper\Data as HelperData;
use Mageplaza\OrderArchive\Model\ResourceModel\Action;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class ActionTest
 * @package Mageplaza\OrderArchive\Test\Unit\Model
 */
class ActionTest extends TestCase
{
    /**
     * @var HelperData|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperData;

    /**
     * @var OrderFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $orderResourceFactory;

    /**
     * @var ResourceConnection|PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var Mysql|PHPUnit_Framework_MockObject_MockObject
     */
    protected $connectionMock;

    /**
     * @var Select|PHPUnit_Framework_MockObject_MockObject
     */
    protected $select;

    /**
     * @var Order|PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var Action|PHPUnit_Framework_MockObject_MockObject
     */
    private $object;

    protected function setUp(): void
    {
        $this->orderResourceFactory = $this->getMockBuilder(OrderFactory::class)->setMethods([
            'create',
            'getTable'
        ])->disableOriginalConstructor()->getMock();
        $this->_helperData          = $this->getMockBuilder(HelperData::class)->disableOriginalConstructor()->getMock();

        $this->resource = $this->getMockBuilder(Order::class)->disableOriginalConstructor()->getMock();
        $this->orderResourceFactory->method('create')->willReturn($this->resource);
        $this->connectionMock = $this->createMock(AdapterInterface::class);
        $this->resource->method('getConnection')->willReturn($this->connectionMock);
        $this->select = $this->createMock(Select::class);

        $this->object = new Action($this->orderResourceFactory, $this->_helperData);
    }

    public function testAdminInstance()
    {
        $this->assertInstanceOf(Action::class, $this->object);
    }

    public function testArchiveOrder()
    {
        $orderId = [1];

        $this->resource->expects($this->at(1))
            ->method('getTable')
            ->with('sales_order')
            ->willReturn('sales_order');
        $this->connectionMock->expects($this->at(0))
            ->method('update')
            ->with(
                'sales_order',
                $this->equalTo(['is_archive' => 1]),
                $this->equalTo(['entity_id IN (?)' => $orderId])
            );

        $this->resource->expects($this->at(2))
            ->method('getTable')
            ->with('sales_order_grid')
            ->willReturn('sales_order_grid');
        $this->connectionMock->expects($this->at(1))
            ->method('update')
            ->with(
                'sales_order_grid',
                $this->equalTo(['is_archive' => 1]),
                $this->equalTo(['entity_id IN (?)' => $orderId])
            );

        $this->object->archiveOrder($orderId);
    }

    public function testGetMatchingOrders()
    {
        $status         = ['pending'];
        $customerGroups = [0];
        $storeIds       = [1];
        $total          = 1000;
        $dayBefore      = 1;
        $orderId        = [1];

        $this->_helperData->method('getOrderStatusConfig')->willReturn($status);
        $this->_helperData->method('getOrderCustomerGroupConfig')->willReturn($customerGroups);
        $this->_helperData->method('getStoreViewConfig')->willReturn($storeIds);

        $this->connectionMock->expects($this->once())->method('select')->willReturn($this->select);
        $this->resource->expects($this->at(1))->method('getTable')->willReturn('sales_order');
        $this->select->expects($this->once())->method('from')->with(['sales_order' => 'sales_order'])->willReturnSelf();

        $this->resource->expects($this->at(2))->method('getTable')->willReturn('sales_order_address');
        $this->select->expects($this->once())->method('joinLeft')->with(
            ['soa' => 'sales_order_address'],
            "sales_order.entity_id = soa.parent_id",
            []
        )->willReturnSelf();

        $this->select->expects($this->at(2))->method('where')
            ->with('sales_order.is_archive = 0 OR sales_order.is_archive IS NULL')
            ->willReturnSelf();
        $this->select->expects($this->at(3))->method('where')
            ->with('sales_order.status IN (?)', $status)
            ->willReturnSelf();
        $this->select->expects($this->at(4))->method('where')
            ->with('sales_order.customer_group_id IN (?)', $customerGroups)
            ->willReturnSelf();

        $this->_helperData->method('getStoreViewConfig')->willReturn($storeIds);

        $this->select->expects($this->at(5))->method('where')
            ->with('sales_order.store_id IN (?)', $storeIds)
            ->willReturnSelf();

        $this->_helperData->method('getOrderTotalConfig')->willReturn($total);
        $this->select->expects($this->at(6))->method('where')
            ->with('sales_order.grand_total <= ?', $total)
            ->willReturnSelf();

        $this->_helperData->method('getPeriodConfig')->willReturn($dayBefore);
        $this->select->expects($this->at(7))->method('where')
            ->with('sales_order.created_at <= ?', $this->_helperData->setDate($dayBefore))
            ->willReturnSelf();

        $type = '1';
        $this->_helperData->method('getShippingCountryType')->willReturn($type);
        $countries = ['VN'];
        $this->_helperData->method('getCountriesConfig')->willReturn($countries);

        $this->select->expects($this->at(8))->method('where')
            ->with('soa.country_id IN (?)', $countries)
            ->willReturnSelf();

        $this->connectionMock->expects($this->once())->method('fetchCol')->with($this->select)->willReturn($orderId);

        $this->assertEquals($orderId, $this->object->getMatchingOrders());
    }
}
