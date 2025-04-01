<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Google Page Speed Optimizer Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Asset\Collector;

class JsCollector extends AbstractAssetCollector
{
    public const REGEX = '/<script[^>]*?src\s*=\s*["|\'](?<asset_url>[^"\']*\.js[^"\']*)["\']+[^>]*?><\/script>/is';

    public function getAssetContentType(): string
    {
        return 'script';
    }
}
