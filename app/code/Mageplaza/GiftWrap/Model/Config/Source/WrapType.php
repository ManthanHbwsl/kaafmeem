<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\GiftWrap\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class WrapType
 * @package Mageplaza\GiftWrap\Model\Config\Source
 */
class WrapType implements ArrayInterface
{
    const GIFT_WRAP_WRAP_TYPE      = 'wrap';
    const GIFT_WRAP_POST_CARD_TYPE = 'postcard';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::GIFT_WRAP_WRAP_TYPE      => __('Gift Wrapping'),
            self::GIFT_WRAP_POST_CARD_TYPE => __('Postcard'),
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
