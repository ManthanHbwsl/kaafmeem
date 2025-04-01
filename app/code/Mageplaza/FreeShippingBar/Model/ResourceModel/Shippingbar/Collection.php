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
 * @package     Mageplaza_FreeShippingBar
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FreeShippingBar\Model\ResourceModel\Shippingbar;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\FreeShippingBar\Model\ResourceModel\Shippingbar;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Status;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @package Mageplaza\FreeShippingBar\Model\ResourceModel\Shippingbar
 */
class Collection extends AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'shippingbar_rule_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'rule_collection';

    /**
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param DateTime $dateTime
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        DateTime $dateTime,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->dateTime = $dateTime;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mageplaza\FreeShippingBar\Model\Shippingbar::class, Shippingbar::class);
    }

    /**
     * Filter expired bar
     */
    public function addDateFilter()
    {
        $now = (new \DateTime())->setTimestamp(strtotime($this->dateTime->gmtDate()));

        $this->addFieldToFilter(
            'from_date',
            [
                ['lteq' => $now],
                ['null' => true]
            ]
        )->addFieldToFilter(
            'to_date',
            [
                ['gteq' => $now],
                ['null' => true]
            ]
        );

        return $this;
    }

    /**
     * @param int $customerGroupId
     *
     * @return $this
     */
    public function addCustomerGroupFilter($customerGroupId)
    {
        $this->addFieldToFilter('customer_group_id', [
            ['finset' => $customerGroupId],
            ['finset' => GroupInterface::CUST_GROUP_ALL]
        ]);

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->addFieldToFilter(
            'store_id',
            [
                ['finset' => $storeId],
                ['finset' => '0']
            ]
        );

        return $this;
    }

    /**
     * Get active rules
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->addFieldToFilter('status', Status::ENABLED);

        return $this;
    }
}
