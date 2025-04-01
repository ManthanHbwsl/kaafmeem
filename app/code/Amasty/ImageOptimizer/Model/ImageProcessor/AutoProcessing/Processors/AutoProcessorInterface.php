<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Image Optimizer for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\ImageProcessor\AutoProcessing\Processors;

interface AutoProcessorInterface
{
    /**
     * @param string $imgPath
     * @return void
     */
    public function execute(string $imgPath): void;
}
