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
 * Interface CategoryInterface
 * @api
 */
interface CategoryInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const CATEGORY_ID    = 'category_id';
    const NAME           = 'name';
    const STATUS         = 'status';
    const DESCRIPTION    = 'description';
    const STORE_ID       = 'store_id';
    const CUSTOMER_GROUP = 'customer_group';
    const SORT_ORDER     = 'sort_order';
    const CREATED_AT     = 'created_at';
    const UPDATED_AT     = 'updated_at';

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCategoryId($value);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDescription($value);

    /**
     * @return string
     */
    public function getStoreId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStoreId($value);

    /**
     * @return string
     */
    public function getCustomerGroup();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCustomerGroup($value);

    /**
     * @return float
     */
    public function getSortOrder();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setSortOrder($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUpdatedAt($value);
}
