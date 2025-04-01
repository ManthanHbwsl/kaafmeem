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

namespace Mageplaza\GiftWrap\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Category;
use Mageplaza\GiftWrap\Model\CategoryFactory;

/**
 * Class AbstractCategory
 * @package Mageplaza\GiftWrap\Controller\Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractCategory extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageplaza_GiftWrap::category';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * AbstractCategory constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Filter $filter
     * @param Data $data
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        Data $data,
        CategoryFactory $categoryFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->filter = $filter;
        $this->data = $data;
        $this->categoryFactory = $categoryFactory;

        parent::__construct($context);
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return Page
     */
    protected function _initAction()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);

        return $resultPage;
    }

    /**
     * Initialize category object
     *
     * @return Category
     */
    protected function _initCategory()
    {
        $category = $this->categoryFactory->create();

        if ($categoryId = $this->getRequest()->getParam('id', 0)) {
            $category->load($categoryId);
        }

        return $category;
    }
}
