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
 * Interface PriceFormatInterface
 * @package Mageplaza\FreeShippingBar\Api\Data
 */
interface PriceFormatInterface
{
    /**
     * @return string
     */
    public function getPattern();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPattern($value);

    /**
     * @return int
     */
    public function getPrecision();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setPrecision($value);

    /**
     * @return int
     */
    public function getRequiredPrecision();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setRequiredPrecision($value);

    /**
     * @return string
     */
    public function getDecimalSymbol();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDecimalSymbol($value);

    /**
     * @return string
     */
    public function getGroupSymbol();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setGroupSymbol($value);

    /**
     * @return int
     */
    public function getGroupLength();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setGroupLength($value);

    /**
     * @return bool
     */
    public function getIntegerRequired();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIntegerRequired($value);
}
