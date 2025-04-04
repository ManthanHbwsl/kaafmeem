<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Member;

use BrainActs\SalesRepresentative\Controller\Adminhtml\Member;

/**
 * Class Products
 *
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Products extends Member
{

    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_member_save';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    private $resultRawFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $member = $this->_initMember();
        if (!$member) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('salesrep/*/', ['_current' => true, 'id' => null]);
        }
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                \BrainActs\SalesRepresentative\Block\Adminhtml\Member\Tab\Product::class,
                'salesrep.member.product.grid'
            )->toHtml()
        );
    }
}
