<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Google Page Speed Optimizer Base for Magento 2
*/
declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Block\Adminhtml\Settings;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Tutorial extends \Magento\Config\Block\System\Config\Form\Field
{
    public function _getElementHtml(AbstractElement $element): string
    {
        /** @var \Magento\Backend\Block\Template $block */
        $block = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Template::class)
            ->setTemplate('Amasty_PageSpeedOptimizer::tutorial.phtml');

        return $block->toHtml();
    }
}
