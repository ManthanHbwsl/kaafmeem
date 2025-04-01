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
 * @package     Mageplaza_GiftWrap
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftWrap\Block\Adminhtml\Widget\Button;

/**
 * Class SplitButton
 * @package Mageplaza\GiftWrap\Block\Adminhtml\Widget\Button
 */
class SplitButton extends \Magento\Backend\Block\Widget\Button\SplitButton
{
    /**
     * @param array $option
     * @param string $title
     * @param string $classes
     * @param string $disabled
     *
     * @return array
     */
    protected function _prepareOptionAttributes($option, $title, $classes, $disabled)
    {
        $attributes = [
            'id'       => isset($option['id']) ? $this->getId() . '-' . $option['id'] : '',
            'title'    => $title,
            'class'    => join(' ', $classes),
            'onclick'  => isset($option['onclick']) ? "setLocation('" . rtrim($option['onclick'], '/') . "')" : '',
            'style'    => isset($option['style']) ? $option['style'] : '',
            'disabled' => $disabled,
        ];

        if (isset($option['data_attribute'])) {
            $this->_getDataAttributes($option['data_attribute'], $attributes);
        }

        return $attributes;
    }

    /**
     * @return $this|\Magento\Backend\Block\Widget\Button\SplitButton|SplitButton
     */
    protected function _beforeToHtml()
    {
        return $this;
    }
}
