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

namespace Mageplaza\OrderArchive\Model\ResourceModel\Order\Grid;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Mageplaza\OrderArchive\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderCollection;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 *
 * @package Mageplaza\OrderArchive\Model\ResourceModel\Order\Grid
 */
class Collection extends OrderCollection
{
    /**
     * Request object
     *
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RequestInterface $_request
     * @param Data $helperData
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $_request,
        Data $helperData,
        $mainTable = 'sales_order_grid',
        $resourceModel = Order::class
    ) {
        $this->_request   = $_request;
        $this->helperData = $helperData;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @return OrderCollection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        if ($this->_request->getFullActionName() !== 'customer_index_orders') {
            $this->getSelect()->where('main_table.is_archive is NULL OR main_table.is_archive = 0');

            if ($this->helperData->isBetterOrderGridEnable() && $this->helperData->isBetterOrderGridHideOrder()) {
                $this->addFieldToFilter('status', ['nin' => $this->helperData->isBetterOrderGridHideOrder()]);
            }
        }
    }
}
