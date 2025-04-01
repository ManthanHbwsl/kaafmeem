<?php
declare(strict_types=1);

namespace Kaafmeem\ImageOptimizerFix\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    private const XML_PATH_DISABLE_RESIZING = 'amimageoptimizer/resize_images/disable_resizing_for_pages';
    private const XML_PATH_PAGE_TYPES = 'amimageoptimizer/resize_images/page_types';
    private const XML_PATH_JPEG_QUALITY = 'amimageoptimizer/resize_images/jpeg_quality';
    private const XML_PATH_WEBP_QUALITY = 'amimageoptimizer/convert_images/webp_quality';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isDisableResizing(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DISABLE_RESIZING);
    }

    public function getPageTypes(): array
    {
        $pageTypes = (string)$this->scopeConfig->getValue(self::XML_PATH_PAGE_TYPES);

        return $pageTypes ? explode(',', $pageTypes) : [];
    }

    public function getJpegQuality(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_JPEG_QUALITY);
    }

    public function getWebpQuality(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_WEBP_QUALITY);
    }
}
