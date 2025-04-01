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
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Wrap;
use Mageplaza\GiftWrap\Model\WrapFactory;

/**
 * Class AbstractWrap
 * @package Mageplaza\GiftWrap\Controller\Adminhtml
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractWrap extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageplaza_GiftWrap::wrap';

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
     * @var WrapFactory
     */
    protected $wrapFactory;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * AbstractWrap constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param Filter $filter
     * @param Data $data
     * @param WrapFactory $wrapFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        Filter $filter,
        Data $data,
        WrapFactory $wrapFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->registry          = $registry;
        $this->filter            = $filter;
        $this->data              = $data;
        $this->wrapFactory       = $wrapFactory;
        $this->mediaDirectory    = $this->data->getMediaDirectory();

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
     * Initialize wrap object
     *
     * @return Wrap
     */
    protected function _initWrap()
    {
        $wrap = $this->wrapFactory->create();
        $wrap->setWrapType($this->getRequest()->getParam('wrap_type'));
        if ($wrapId = $this->getRequest()->getParam('id', 0)) {
            $wrap->load($wrapId);
        }

        return $wrap;
    }
}
