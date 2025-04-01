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
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\OrderArchive\Helper\Data as HelperData;
use Mageplaza\OrderArchive\Model\ResourceModel\Action;

/**
 * Class MassArchive
 *
 * @package Mageplaza\OrderArchive\Controller\Adminhtml\Archive
 */
class MassArchive extends AbstractMassAction
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::archive';

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var Action
     */
    protected $_action;

    /**
     * MassArchive constructor.
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param HelperData $helperData
     * @param Action $action
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        HelperData $helperData,
        Action $action
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->_helperData       = $helperData;
        $this->_action           = $action;

        parent::__construct($context, $filter);
    }

    /**
     * @param AbstractCollection $collection
     *
     * @return Redirect|ResponseInterface|ResultInterface
     */
    protected function massAction(AbstractCollection $collection)
    {
        $archived = 0;

        /** @var OrderInterface $order */
        foreach ($collection->getItems() as $order) {
            try {
                $this->_action->archiveOrder($order->getId());
                $archived++;
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(
                    __(
                        'Cannot archive order #%1. Please try again later. %2',
                        $order->getIncrementId(),
                        $e->getMessage()
                    )
                );
            }
        }

        if ($archived) {
            $this->messageManager->addSuccessMessage(__('A total of %1 order(s) has been archived.', $archived));
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
