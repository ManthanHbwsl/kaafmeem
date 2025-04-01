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

namespace Mageplaza\GiftWrap\Controller\Adminhtml\Wrap\Sync;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Phrase;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class EstimateHistory
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Wrap\Sync
 */
class EstimateHistory extends Action
{
    /**
     * @var OrderCollection
     */
    private $orderCollectionFactory;

    /**
     * EstimateHistory constructor.
     *
     * @param Context $context
     * @param OrderCollection $orderCollectionFactory
     */
    public function __construct(Context $context, OrderCollection $orderCollectionFactory)
    {
        $this->orderCollectionFactory = $orderCollectionFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $collection      = $this->prepareCollection();
        $ids             = $collection->getAllIds();
        $result['ids']   = $ids;
        $result['total'] = count($ids);

        if ($result['total'] === 0) {
            $result['message'] = $this->getZeroMessage();
        }

        $result['status'] = true;

        return $this->getResponse()->representJson(Data::jsonEncode($result));
    }

    /**
     * @return Collection
     */
    public function prepareCollection()
    {
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->getSelect()->joinInner(
            ['wrap_history' => $orderCollection->getTable('mageplaza_giftwrap_history')],
            'main_table.entity_id = wrap_history.order_id
            AND (wrap_history.mp_gift_wrap_invoice_base_amount IS NULL
            OR wrap_history.mp_gift_wrap_creditmemo_base_amount IS NULL)',
            []
        )->joinInner(
            ['sales_invoice' => $orderCollection->getTable('sales_invoice')],
            'main_table.entity_id = sales_invoice.order_id',
            []
        )->group('main_table.entity_id');

        return $orderCollection;
    }

    /**
     * @return Phrase
     */
    public function getZeroMessage()
    {
        return __('No Histories to synchronize.');
    }
}
