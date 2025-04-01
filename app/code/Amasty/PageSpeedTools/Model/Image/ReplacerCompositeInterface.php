<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Page Speed Tools for Magento 2 (System)
*/

namespace Amasty\PageSpeedTools\Model\Image;

interface ReplacerCompositeInterface
{
    public const REPLACE_BEST = 'replace_with_best';
    public const REPLACE_PICTURE = 'replace_with_picture';

    public function replace(string $algorithm, string $image, string $imagePath): string;

    public function replaceImagePath(string $algorithm, string $imagePath): string;
}
