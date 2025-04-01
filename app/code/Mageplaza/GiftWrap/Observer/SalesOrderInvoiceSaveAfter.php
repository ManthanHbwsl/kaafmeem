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
use Magento\Sales\Model\Order\Invoice;
use Mageplaza\GiftWrap\Helper\Data as HelperData;
use Mageplaza\GiftWrap\Model\ResourceModel\History\CollectionFactory;

/**
 * Class SalesOrderInvoiceSaveAfter
 * @package Mageplaza\GiftWrap\Observer
 */
class SalesOrderInvoiceSaveAfter implements ObserverInterface
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
     * SalesOrderInvoiceSaveAfter constructor.
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
        /* @var $invoice Invoice */
        $invoice           = $observer->getEvent()->getInvoice();
        $invoiceCollection = $invoice->getOrder()->getInvoiceCollection();
        $historyCollection = $this->collectionFactory->create()->load($invoice->getOrderId());
        if ($historyCollection->getSize()) {
            $historyData = [];
            $this->helperData->updateFromCollection(
                $invoiceCollection,
                $historyData
            );
            $this->helperData->updateHistoryTransaction($historyData);
        }
    }
}
