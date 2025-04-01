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

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\Order;
use Mageplaza\GiftWrap\Helper\Data;
use Magento\Sales\Model\OrderFactory;

/**
 * Class History
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Sync\Wrap
 */
class History extends Action
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * History constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        Data $helperData,
        OrderFactory $orderFactory
    ) {
        $this->helperData   = $helperData;
        $this->orderFactory = $orderFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $result = [];
        try {
            $ids              = $this->getRequest()->getParam('ids');
            $result['status'] = true;
            $result['total']  = count($ids);

            $historyData = [];
            foreach ($ids as $id) {
                /** @var Order $order */
                $order = $this->orderFactory->create()->load($id);
                $this->helperData->updateFromCollection($order->getInvoiceCollection(), $historyData);
                if ($creditmemoCollection = $order->getCreditmemosCollection()) {
                    $this->helperData->updateFromCollection($creditmemoCollection, $historyData);
                }
            }

            $this->helperData->updateHistoryTransaction($historyData);
        } catch (Exception $e) {
            $result['status']  = false;
            $result['message'] = $e->getMessage();
        }

        return $this->getResponse()->representJson(Data::jsonEncode($result));
    }
}
