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

namespace Mageplaza\OrderArchive\Model\ResourceModel;

use Magento\Sales\Model\ResourceModel\OrderFactory;
use Mageplaza\OrderArchive\Helper\Data as HelperData;
use Mageplaza\OrderArchive\Model\Config\Source\Country;

/**
 * Class Action
 * @package Mageplaza\OrderArchive\Model\ResourceModel
 */
class Action
{
    const ALL_STORE_ID = '0';

    /**
     * @var OrderFactory
     */
    private $orderResourceFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Action constructor.
     *
     * @param OrderFactory $orderResourceFactory
     * @param HelperData $helperData
     */
    public function __construct(
        OrderFactory $orderResourceFactory,
        HelperData $helperData
    ) {
        $this->orderResourceFactory = $orderResourceFactory;
        $this->_helperData          = $helperData;
    }

    /**
     * @param array $orderId
     *
     * @return $this
     */
    public function archiveOrder($orderId = [])
    {
        $resource   = $this->orderResourceFactory->create();
        $connection = $resource->getConnection();

        $connection->update(
            $resource->getTable('sales_order'),
            ['is_archive' => 1],
            ['entity_id IN (?)' => $orderId]
        );

        $connection->update(
            $resource->getTable('sales_order_grid'),
            ['is_archive' => 1],
            ['entity_id IN (?)' => $orderId]
        );

        return $this;
    }

    /**
     * @param string $orderId
     *
     * @return $this
     */
    public function unarchiveOrder($orderId)
    {
        $resource   = $this->orderResourceFactory->create();
        $connection = $resource->getConnection();

        $connection->update(
            $resource->getTable('sales_order'),
            ['is_archive' => 0],
            ['entity_id = ?' => $orderId]
        );

        $connection->update(
            $resource->getTable('sales_order_grid'),
            ['is_archive' => 0],
            ['entity_id = ?' => $orderId]
        );

        return $this;
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getMatchingOrders($storeId = null)
    {
        $resource       = $this->orderResourceFactory->create();
        $connection     = $resource->getConnection();
        $status         = $this->_helperData->getOrderStatusConfig($storeId);
        $customerGroups = $this->_helperData->getOrderCustomerGroupConfig($storeId);
        $storeIds       = $this->_helperData->getStoreViewConfig($storeId);

        $select = $connection->select()
            ->from(['sales_order' => $resource->getTable('sales_order')])
            ->joinLeft(
                ['soa' => $resource->getTable('sales_order_address')],
                'sales_order.entity_id = soa.parent_id',
                []
            )
            ->where('sales_order.is_archive = 0 OR sales_order.is_archive IS NULL')
            ->where('sales_order.status IN (?)', $status)
            ->where('sales_order.customer_group_id IN (?)', $customerGroups);

        if (!in_array(self::ALL_STORE_ID, $storeIds, true)) {
            $select->where('sales_order.store_id IN (?)', $storeIds);
        }

        $total = $this->_helperData->getOrderTotalConfig($storeId);
        if ($total) {
            $select->where('sales_order.grand_total <= ?', $total);
        }

        $dayBefore = $this->_helperData->getPeriodConfig($storeId);
        if ($dayBefore) {
            $select->where('sales_order.created_at <= ?', $this->_helperData->setDate($dayBefore));
        }

        $type = $this->_helperData->getShippingCountryType($storeId);
        if ($type === Country::SPECIFIC) {
            $countries = $this->_helperData->getCountriesConfig($storeId);
            $select->where('soa.country_id IN (?)', $countries);
        }

        return array_unique($connection->fetchCol($select));
    }

    /**
     * Get All Order Ids
     * @return array
     */
    public function getAllOrderIds()
    {
        $resource   = $this->orderResourceFactory->create();
        $connection = $resource->getConnection();
        $select     = $connection->select()->from($resource->getTable('sales_order'));

        return array_unique($connection->fetchCol($select));
    }

    /**
     * check Order Id include order ids are Archived on Magento EE
     *
     * @param string $orderId
     *
     * @return bool
     */
    public function checkOrderIdsArchiveEE($orderId)
    {
        $resource   = $this->orderResourceFactory->create();
        $connection = $resource->getConnection();

        if ($connection->isTableExists($resource->getTable('magento_sales_order_grid_archive')) === false) {
            return false;
        }

        $select = $connection->select()
            ->from($resource->getTable('magento_sales_order_grid_archive'));

        $orderIds = array_unique($connection->fetchCol($select));

        return in_array($orderId, $orderIds, true);
    }
}
