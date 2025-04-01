<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Lazy Load for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\LazyLoad\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class PreloadImages extends Field
{
    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Context $context,
        Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!$this->moduleManager->isEnabled('Amasty_PageSpeedOptimizer')) {
            $tooltip = __(
                'If enabled the specified number of images will be excluded ' .
                'from Lazy Load and will be loaded along with the page content.'
            )->render();
        } else {
            $tooltip = __(
                'If enabled the specified number of images will be excluded from Lazy Load and will be' .
                ' loaded along with the page content. <br/>For images preload request before content,' .
                ' please make sure that Server Push is enabled and Preloaded images option is selected in the' .
                ' Asset Types to Server Push setting (to configure proceed to ' .
                'Stores -> Configuration -> Amasty Extensions -> Google Page Speed Optimizer -> Server Push).'
            )->render();
        }
        $element->setTooltip($tooltip);

        return parent::render($element);
    }
}
