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

namespace Mageplaza\OrderArchive\Block\Order;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Mageplaza\OrderArchive\Helper\Data as HelperData;

/**
 * Class Recent
 *
 * @package Mageplaza\OrderArchive\Block\Order
 */
class Recent extends Template
{
    /**
     * Limit of orders
     */
    const ORDER_LIMIT = 5;

    /**
     * @var CollectionFactory
     */
    protected $_orderCollFactory;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var Config
     */
    protected $_orderConfig;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Recent constructor.
     *
     * @param Context $context
     * @param CollectionFactory $orderCollFactory
     * @param Session $customerSession
     * @param Config $orderConfig
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $orderCollFactory,
        Session $customerSession,
        Config $orderConfig,
        HelperData $helperData,
        array $data = []
    ) {
        $this->_orderCollFactory = $orderCollFactory;
        $this->_customerSession  = $customerSession;
        $this->_orderConfig      = $orderConfig;
        $this->_isScopePrivate   = true;
        $this->_helperData       = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * Set orders
     */
    protected function _construct()
    {
        parent::_construct();

        $orders = $this->_orderCollFactory->create()->addAttributeToSelect(
            '*'
        )->addAttributeToFilter(
            'customer_id',
            $this->_customerSession->getCustomerId()
        )->addAttributeToFilter(
            'store_id',
            $this->_storeManager->getStore()->getId()
        )->addAttributeToFilter(
            'status',
            ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
        )->addAttributeToSort(
            'created_at',
            'desc'
        )->setPageSize(
            self::ORDER_LIMIT
        );

        if (!$this->_helperData->isShowArchive()) {
            $orders->getSelect()->where('is_archive = 0 OR is_archive IS NULL');
        }

        $orders->load();

        $this->setOrders($orders);
    }

    /**
     * @param object $order
     *
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     *
     * @return string
     */
    public function getTrackUrl($order)
    {
        return $this->getUrl('sales/order/track', ['order_id' => $order->getId()]);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getOrders()->getSize() > 0) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * @param object $order
     *
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }
}
