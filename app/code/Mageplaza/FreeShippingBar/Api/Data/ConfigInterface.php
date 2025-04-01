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

namespace Mageplaza\FreeShippingBar\Api\Data;

/**
 * Interface ConfigInterface
 * @package Mageplaza\FreeShippingBar\Api\Data
 */
interface ConfigInterface
{
    /**
     * @return \Mageplaza\FreeShippingBar\Api\Data\PriceFormatInterface
     */
    public function getPriceFormat();

    /**
     * @param \Mageplaza\FreeShippingBar\Api\Data\PriceFormatInterface $value
     *
     * @return $this
     */
    public function setPriceFormat($value);
}
