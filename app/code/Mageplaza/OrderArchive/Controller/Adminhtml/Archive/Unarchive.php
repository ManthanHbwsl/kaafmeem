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

namespace Mageplaza\OrderArchive\Controller\Adminhtml\Archive;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Mageplaza\OrderArchive\Model\ResourceModel\Action as OrderAction;

/**
 * Class Unarchive
 *
 * @package Mageplaza\OrderArchive\Controller\Adminhtml\Archive
 */
class Unarchive extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::unarchive';

    /**
     * @var OrderAction
     */
    protected $_action;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFac;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Unarchive constructor.
     *
     * @param Action\Context $context
     * @param OrderAction $action
     * @param RedirectFactory $resultRedirectFac
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        OrderAction $action,
        RedirectFactory $resultRedirectFac,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->_action           = $action;
        $this->resultRedirectFac = $resultRedirectFac;
        $this->orderRepository   = $orderRepository;

        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFac->create();
        $orderId        = $this->_request->getParam('order_id');

        try {
            $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This order no longer exists.'));

            return $resultRedirect->setPath('sales/archive/index');
        }

        try {
            $this->_action->unarchiveOrder($orderId);
            $this->messageManager->addSuccessMessage(__('The order has been unarchived.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred while unarchiving the order. Please try again later.', $e->getMessage())
            );

            return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        }

        return $resultRedirect->setPath('sales/archive/index');
    }
}
