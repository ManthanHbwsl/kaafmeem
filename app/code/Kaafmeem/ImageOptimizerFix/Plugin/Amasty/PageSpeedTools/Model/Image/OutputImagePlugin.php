<?php
declare(strict_types=1);

namespace Kaafmeem\ImageOptimizerFix\Plugin\Amasty\PageSpeedTools\Model\Image;

use Amasty\PageSpeedTools\Model\Image\OutputImage;
use Kaafmeem\ImageOptimizerFix\Model\ConfigProvider;
use Kaafmeem\ImageOptimizerFix\Model\PageType\PageValidator;

class OutputImagePlugin
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var PageValidator
     */
    private $pageValidator;

    public function __construct(
        ConfigProvider $configProvider,
        PageValidator $pageValidator
    ) {
        $this->configProvider = $configProvider;
        $this->pageValidator = $pageValidator;
    }

    public function afterSetImageData(OutputImage $subject, $result): void
    {
        if ($this->configProvider->isDisableResizing()
            && $this->pageValidator->isValid($this->configProvider->getPageTypes())
        ) {
            $subject->addData(
                [
                    'webp_tablet_path' => false,
                    'tablet_path' => false,
                    'webp_mobile_path' => false,
                    'mobile_path' => false
                ]
            );
        }
    }
}
