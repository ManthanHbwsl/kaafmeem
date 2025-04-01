<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Page Speed Tools for Magento 2 (System)
*/

namespace Amasty\PageSpeedTools\Model\Image;

interface ReplacerInterface
{
    public function execute(string $image, string $imagePath): string;

    public function getReplaceImagePath(string $imagePath): string;
}
