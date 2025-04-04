<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-banner
 * @version   1.1.10
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Banner\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Repository\BannerRepository;

abstract class AbstractBanner extends Action
{
    protected $bannerRepository;

    private $registry;

    protected $context;

    public function __construct(
        BannerRepository $bannerRepository,
        Registry $registry,
        Context $context
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->registry         = $registry;
        $this->context          = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Backend::marketing');
        $resultPage->getConfig()->getTitle()->prepend(__('Banners'));

        return $resultPage;
    }

    /**
     * @return BannerInterface
     */
    public function initModel()
    {
        $model = $this->bannerRepository->create();

        if ($this->getRequest()->getParam(BannerInterface::ID)) {
            $model = $this->bannerRepository->get($this->getRequest()->getParam(BannerInterface::ID));
        }

        $this->registry->register(BannerInterface::class, $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Banner::banner_banner');
    }
}
