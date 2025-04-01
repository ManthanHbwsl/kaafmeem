<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Google Page Speed Optimizer Base for Magento 2
*/

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Block\Adminhtml\Settings;

class ProductionModeInfo extends CommonInfoField
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_PageSpeedOptimizer::production_mode_info.phtml';
}
