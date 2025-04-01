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

namespace Mageplaza\FreeShippingBar\Model\Shippingbar;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Position
 * @package Mageplaza\FreeShippingBar\Model\Shippingbar
 */
class Position implements ArrayInterface
{
    const TOP_PAGE = 'top_page';
    const TOP_FIXED_BAR = 'top_fixed_bar';
    const TOP_CONTENT = 'top_content';
    const BOTTOM_FIXED_BAR = 'bottom_fixed_bar';
    const INSERT_SNIPPET = 'insert_snippet';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TOP_PAGE,
                'label' => __('The top of the page')
            ],
            [
                'value' => self::TOP_FIXED_BAR,
                'label' => __('Fixed bar at the top of the page')
            ],
            [
                'value' => self::TOP_CONTENT,
                'label' => __('The top of the content')
            ],
            [
                'value' => self::BOTTOM_FIXED_BAR,
                'label' => __('Fixed bar at the bottom of the page')
            ],
            [
                'value' => self::INSERT_SNIPPET,
                'label' => __('Insert Snippet')
            ],
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $options = [];
        foreach ($this->toOptionArray() as $option) {
            $options[] = $option['value'];
        }

        return $options;
    }
}
