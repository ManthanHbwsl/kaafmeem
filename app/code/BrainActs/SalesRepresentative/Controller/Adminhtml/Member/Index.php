<?php

/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Member;

/**
 * Class Index
 *
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Index extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_member';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    private function initPage($resultPage)
    {
        $resultPage->setActiveMenu('BrainActs_SalesRepresentative::sales_representative_member');
        return $resultPage;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Members'));
        return $resultPage;
    }
}
