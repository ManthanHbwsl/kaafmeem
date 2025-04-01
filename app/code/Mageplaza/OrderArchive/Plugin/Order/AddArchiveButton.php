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

namespace Mageplaza\OrderArchive\Plugin\Order;

use Closure;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View;
use Magento\Sales\Model\OrderFactory;
use Mageplaza\OrderArchive\Helper\Data as HelperData;
use Mageplaza\OrderArchive\Model\ResourceModel\Action;

/**
 * Class AddArchiveButton
 *
 * @package Mageplaza\OrderArchive\Plugin\Order
 */
class AddArchiveButton
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var Action
     */
    protected $_action;

    /**
     * AddArchiveButton constructor.
     *
     * @param HelperData $helperData
     * @param AuthorizationInterface $authorization
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     * @param OrderFactory $orderFactory
     * @param ManagerInterface $messageManager
     * @param Action $action
     */
    public function __construct(
        HelperData $helperData,
        AuthorizationInterface $authorization,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        OrderFactory $orderFactory,
        ManagerInterface $messageManager,
        Action $action
    ) {
        $this->_helperData     = $helperData;
        $this->_authorization  = $authorization;
        $this->_urlBuilder     = $urlBuilder;
        $this->_request        = $request;
        $this->_orderFactory   = $orderFactory;
        $this->_messageManager = $messageManager;
        $this->_action         = $action;
    }

    /**
     * @param View $object
     * @param LayoutInterface $layout
     *
     * @return array
     */
    public function beforeSetLayout(View $object, LayoutInterface $layout)
    {
        if (!$this->_helperData->isEnabled()) {
            return [$layout];
        }

        $orderId = $object->getOrderId();
        if ($this->_action->checkOrderIdsArchiveEE($orderId)) {
            return [$layout];
        }

        $isArchive = $this->isArchive($orderId);
        if ($isArchive) {
            $this->removeDefaultButtons($object);
            $this->_messageManager->getMessages(true);
            $this->_messageManager->addNoticeMessage(__('This order has been archived.'));

            if ($this->_authorization->isAllowed('Magento_Sales::unarchive')) {
                $message = __('Are you sure to unarchive this order?');
                $object->addButton('order_un_archive', [
                    'label'   => __('Unarchive'),
                    'class'   => 'un_archive',
                    'id'      => 'mporderarchive-view-un-archive-button',
                    'onclick' => "confirmSetLocation('{$message}', '{$this->getUnarchiveUrl()}')"
                ]);
            }
        } elseif ($this->_authorization->isAllowed('Magento_Sales::archive')) {
            $message = __('Are you sure to archive this order?');
            $object->addButton('order_archive', [
                'label'   => __('Archive'),
                'class'   => 'archive',
                'id'      => 'mporderarchive-view-archive-button',
                'onclick' => "confirmSetLocation('{$message}', '{$this->getArchiveUrl()}')"
            ]);
        }

        return [$layout];
    }

    /**
     * @param View $object
     */
    public function removeDefaultButtons(View $object)
    {
        $defaultButtons = [
            'order_edit',
            'order_cancel',
            'send_notification',
            'order_creditmemo',
            'void_payment',
            'order_hold',
            'order_unhold',
            'accept_payment',
            'deny_payment',
            'get_review_payment_update',
            'order_invoice',
            'order_ship',
            'order_reorder'
        ];

        foreach ($defaultButtons as $button) {
            $object->removeButton($button);
        }
    }

    /**
     * @param  $subject
     * @param Closure $proceed
     *
     * @return mixed
     */
    public function aroundGetBackUrl(View $subject, Closure $proceed)
    {
        if ($this->_helperData->isEnabled()) {
            $orderId   = $this->_request->getParam('order_id');
            $isArchive = $this->isArchive($orderId);

            if ($isArchive) {
                if ($subject->getOrder() && $subject->getOrder()->getBackUrl()) {
                    return $subject->getOrder()->getBackUrl();
                }

                return $this->_urlBuilder->getUrl('sales/archive/index');
            }
        }

        return $proceed();
    }

    /**
     * @param string $orderId
     *
     * @return bool
     */
    public function isArchive($orderId)
    {
        $order = $this->_orderFactory->create()->load($orderId);

        return (bool) $order->getData('is_archive');
    }

    /**
     * @return string
     */
    public function getArchiveUrl()
    {
        return $this->_urlBuilder
            ->getUrl('sales/archive/archive', ['order_id' => $this->_request->getParam('order_id')]);
    }

    /**
     * @return string
     */
    public function getUnarchiveUrl()
    {
        return $this->_urlBuilder
            ->getUrl('sales/archive/unarchive', ['order_id' => $this->_request->getParam('order_id')]);
    }
}
