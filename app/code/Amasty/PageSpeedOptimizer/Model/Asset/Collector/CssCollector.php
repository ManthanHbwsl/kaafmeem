<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Google Page Speed Optimizer Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Asset\Collector;

class CssCollector extends AbstractAssetCollector
{
    public const REGEX = '/<link[^>]*href\s*=\s*["|\'](?<asset_url>[^"\']*\.css[^"\']*)["\']+[^>]*>/is';

    public function getAssetContentType(): string
    {
        return 'style';
    }
}
