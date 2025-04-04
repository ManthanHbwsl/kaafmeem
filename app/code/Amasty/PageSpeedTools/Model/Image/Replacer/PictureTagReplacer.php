<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Page Speed Tools for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Image\Replacer;

use Amasty\PageSpeedTools\Model\Image\OutputImage;
use Amasty\PageSpeedTools\Model\Image\ReplacerInterface;

class PictureTagReplacer implements ReplacerInterface
{
    /**
     * @var OutputImage
     */
    private $outputImage;

    public function __construct(OutputImage $outputImage)
    {
        $this->outputImage = $outputImage;
    }

    public function execute(string $image, string $imagePath): string
    {
        $outputImage = $this->outputImage->initialize($imagePath);

        if ($outputImage->process() && $sourceSet = $outputImage->getSourceSet()) {
            return '<picture>' . $sourceSet . $image . '</picture>';
        }

        return $image;
    }

    public function getReplaceImagePath(string $imagePath): string
    {
        return $imagePath;
    }
}
