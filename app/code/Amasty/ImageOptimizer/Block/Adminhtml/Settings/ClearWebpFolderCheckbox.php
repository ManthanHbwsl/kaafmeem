<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Image Optimizer for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Block\Adminhtml\Settings;

use Magento\Framework\Data\Form\Element\AbstractElement;

class ClearWebpFolderCheckbox extends CommonCheckbox
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $element->setData('value', __("WebP Images Folder"));
        $element->setData('class', "amoptimizer-checkbox");
        $element->setData('name', "webp");

        return parent::_getElementHtml($element);
    }
}
