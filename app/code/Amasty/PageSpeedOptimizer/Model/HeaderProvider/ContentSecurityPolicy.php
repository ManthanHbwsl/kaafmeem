<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Google Page Speed Optimizer Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\HeaderProvider;

use Magento\Framework\App\Response\HeaderProvider\AbstractHeaderProvider;

class ContentSecurityPolicy extends AbstractHeaderProvider
{
    /**
     * @var IsSetXFrameOptions
     */
    private $isSetXFrameOptions;

    public function __construct(
        IsSetXFrameOptions $isSetXFrameOptions
    ) {
        $this->isSetXFrameOptions = $isSetXFrameOptions;
    }

    public function canApply(): bool
    {
        return (bool)$this->isSetXFrameOptions->isSetHeader();
    }

    public function getName(): string
    {
        return 'Content-Security-Policy';
    }

    public function getValue(): string
    {
        return 'frame-ancestors ' . $this->isSetXFrameOptions->getBaseUrl();
    }
}
