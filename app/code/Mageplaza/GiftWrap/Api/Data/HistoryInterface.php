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

namespace Mageplaza\GiftWrap\Api\Data;

/**
 * Interface HistoryInterface
 * @api
 */
interface HistoryInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const HISTORY_ID   = 'history_id';
    const ORDER_ID     = 'order_id';
    const INCR_ID      = 'incr_id';
    const WRAP_ID      = 'wrap_id';
    const WRAP         = 'wrap';
    const CATEGORY     = 'category';
    const IMAGE        = 'image';
    const PRODUCT_LIST = 'product_list';
    const MESSAGE      = 'message';
    const ORDER_DATE   = 'order_date';

    /**
     * @return int
     */
    public function getHistoryId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setHistoryId($value);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setOrderId($value);

    /**
     * @return string
     */
    public function getIncrId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setIncrId($value);

    /**
     * @return int
     */
    public function getWrapId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setWrapId($value);

    /**
     * @return string
     */
    public function getWrap();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setWrap($value);

    /**
     * @return string
     */
    public function getCategory();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCategory($value);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setImage($value);

    /**
     * @return string
     */
    public function getProductList();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setProductList($value);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMessage($value);

    /**
     * @return string
     */
    public function getOrderDate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOrderDate($value);
}
