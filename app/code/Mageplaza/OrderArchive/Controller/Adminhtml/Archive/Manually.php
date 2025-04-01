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
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\OrderArchive\Helper\Data as HelperData;
use Mageplaza\OrderArchive\Model\ResourceModel\Action as OrderAction;

/**
 * Class Manually
 *
 * @package Mageplaza\OrderArchive\Controller\Adminhtml\Archive
 */
class Manually extends Action
{
    /**
     * @var OrderAction
     */
    protected $_action;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * Manually constructor.
     *
     * @param Action\Context $context
     * @param OrderAction $action
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        OrderAction $action,
        HelperData $helperData
    ) {
        $this->_action     = $action;
        $this->_helperData = $helperData;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $storeId        = $this->getRequest()->getParam('store');
        $orderIds       = $this->_action->getMatchingOrders($storeId);

        if ($this->_helperData->isEnabled($storeId)) {
            try {
                $this->_action->archiveOrder($orderIds);

                $this->messageManager->addSuccessMessage(__('Success!'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('An error occurred while running manually. Please try again later. %1', $e->getMessage())
                );
            }
        } else {
            $this->messageManager->addNoticeMessage(__('Please enable module!'));
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
