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



namespace Mirasvit\Banner\Block\Placeholder;

use Magento\Framework\View\Element\Template;
use Mirasvit\Banner\Api\Data\BannerInterface;
use Mirasvit\Banner\Api\Data\PlaceholderInterface;
use Mirasvit\Banner\Service\BannerService;

abstract class AbstractRenderer extends Template
{
    protected $bannerService;
    protected $serializer;

    public function __construct(
        BannerService $bannerService,
        Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        array $data = []
    ) {
        $this->bannerService = $bannerService;
        $this->serializer = $serializer;

        parent::__construct($context, $data);
    }

    public function jsonEncode($data) {
        return $this->serializer->serialize($data);
    }

    /**
     * @return PlaceholderInterface
     */
    public function getPlaceholder()
    {
        return $this->getData(PlaceholderInterface::class);
    }

    public function getBannerHtml(BannerInterface $banner)
    {
        return $this->bannerService->toHtml($banner);
    }
}
