<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_FreeShippingBar
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FreeShippingBar\Block\Adminhtml\Render\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Codehtml
 * @package Mageplaza\FreeShippingBar\Block\Adminhtml\Render\Field
 */
class Codehtml extends AbstractElement
{
    /**
     * Get element html
     * @return string
     */
    public function getElementHtml()
    {
        $subject = $this->getData('subject');

        $currentShippingBar = $subject->getCurrentShippingbar();
        $ruleId = $currentShippingBar->getRuleId() ? (int)$currentShippingBar->getRuleId() : '';

        $html = '<div id="shippingbar_snippet_code_insert" class="control-value" style="padding-top: 8px">';
        $html .= '<span><p>'
            . __('Use the following code to show the shipping bar block in any place you want')
            . '</p></span>';

        $html .= '<strong>' . __('CMS Page/Static Block') . '</strong><br />';
        $html .= '<span style="font-size: 10px"><pre style="background-color: #f5f5dc"><code>'
            . '{{block class="Mageplaza\FreeShippingBar\Block\FreeShippingBar"'
            . ' template="Mageplaza_FreeShippingBar::insertshippingbar.phtml" rule_id="'
            . $ruleId . '"}}</code></pre></span>';

        $html .= '<strong>' . __('Template .phtml file') . '</strong><br />';
        $html .= '<span style="font-size: 10px"><pre style="background-color: #f5f5dc"><code>'
            . $this->_escaper->escapeHtml(
                '<?php echo $block->getLayout()->createBlock("Mageplaza\FreeShippingBar\Block\FreeShippingBar")'
                . '->setTemplate("insertshippingbar.phtml")->setRuleId("' . $ruleId . '")->toHtml();?>'
            ) . '</code></pre></span>';

        $html .= '<strong>' . __('Layout file') . '</strong><br />';
        $html .= '<span style="font-size: 10px"><pre style="background-color: #f5f5dc"><code>' . $this->_escaper->escapeHtml('
            <block class="Mageplaza\FreeShippingBar\Block\FreeShippingBar" template="Mageplaza_FreeShippingBar::insertshippingbar.phtml" name="mp_freeshippingbar_insert">
                <arguments>
                    <argument name="rule_id" xsi:type="string">' . $ruleId . '</argument>
                </arguments>
            </block>
            ') . '</code></pre></span>';

        $html .= '</div>';

        return $html;
    }
}
