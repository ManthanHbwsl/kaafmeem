<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Page Speed Tools for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Image\Replacer;

use Amasty\PageSpeedTools\Model\DeviceDetect;
use Amasty\PageSpeedTools\Model\Image\OutputImage;
use Amasty\PageSpeedTools\Model\Image\ReplacerInterface;

class BestReplacer implements ReplacerInterface
{
    /**
     * @var OutputImage
     */
    private $outputImage;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    public function __construct(
        OutputImage $outputImage,
        DeviceDetect $deviceDetect
    ) {
        $this->outputImage = $outputImage;
        $this->deviceDetect = $deviceDetect;
    }

    public function execute(string $image, string $imagePath): string
    {
        $replacedImagePath = $this->getReplaceImagePath($imagePath);
        if ($replacedImagePath != $imagePath) {
            return str_replace(
                $imagePath,
                $replacedImagePath,
                $image
            );
        }

        return $image;
    }

    public function getReplaceImagePath(string $imagePath): string
    {
        $outputImage = $this->outputImage->initialize($imagePath);
        if ($outputImage->process()) {
            $imagePath = $outputImage->getBest(...$this->deviceDetect->getDeviceParams());
        }

        return $imagePath;
    }
}
