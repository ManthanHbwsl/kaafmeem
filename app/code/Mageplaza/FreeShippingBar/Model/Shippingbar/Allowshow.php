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
 * Class Allowshow
 * @package Mageplaza\FreeShippingBar\Model\Shippingbar
 */
class Allowshow implements ArrayInterface
{
    const ALL_PAGES = 'all_pages';
    const SPECIFIC_PAGES = 'specific_pages';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ALL_PAGES,
                'label' => __('All Pages')
            ],
            [
                'value' => self::SPECIFIC_PAGES,
                'label' => __('Specific Pages')
            ],
        ];

        return $options;
    }
}
