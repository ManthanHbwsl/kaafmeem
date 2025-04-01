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

namespace Mageplaza\GiftWrap\Observer;

use Exception;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Mageplaza\GiftWrap\Helper\Data as HelperData;
use Mageplaza\GiftWrap\Model\ResourceModel\History\CollectionFactory;

/**
 * Class SalesOrderCreditmemoSaveAfter
 * @package Mageplaza\GiftWrap\Observer
 */
class SalesOrderCreditmemoSaveAfter implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * SalesOrderCreditmemoSaveAfter constructor.
     *
     * @param HelperData $helperData
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(HelperData $helperData, CollectionFactory $collectionFactory)
    {
        $this->helperData        = $helperData;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param EventObserver $observer
     *
     * @throws Exception
     */
    public function execute(EventObserver $observer)
    {
        /* @var $creditmemo Creditmemo */
        $creditmemo        = $observer->getEvent()->getCreditmemo();
        $historyCollection = $this->collectionFactory->create()->load($creditmemo->getOrderId());
        if ($historyCollection->getSize()) {
            $historyData = [];
            $this->helperData->updateWrapHistoryData(
                $creditmemo,
                $historyData
            );
            $this->helperData->updateHistoryTransaction($historyData);
        }
    }
}
