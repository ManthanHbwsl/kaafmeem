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

use Magento\Framework\Data\OptionSourceInterface;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class GiftMessageScope
 * @package Mageplaza\StoreCredit\Model\Config\Source
 */
class GiftMessageScope implements OptionSourceInterface
{
    const CART = 1;
    const CHECKOUT = 2;
    const OSC = 3;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * GiftMessageScope constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        $result = [
            self::CART => __('Shopping Cart Page'),
            self::CHECKOUT => __('Checkout Page')
        ];

        if ($this->helper->isModuleOutputEnabled('Mageplaza_Osc')) {
            $result[self::OSC] = __('Mageplaza One Step Checkout');
        }

        return $result;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $result = [];

        foreach ($this->getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
