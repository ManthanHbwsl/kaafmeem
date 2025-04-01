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
 * Interface WrapInterface
 * @api
 */
interface WrapInterface
{
    /**
     * Constants defined for keys of array, makes typos less likely
     */
    const WRAP_ID     = 'wrap_id';
    const NAME        = 'name';
    const STATUS      = 'status';
    const PRICE_TYPE  = 'price_type';
    const AMOUNT      = 'amount';
    const DESCRIPTION = 'description';
    const IMAGE       = 'image';
    const CATEGORY    = 'category';
    const SORT_ORDER  = 'sort_order';
    const CREATED_AT  = 'created_at';
    const UPDATED_AT  = 'updated_at';
    const MEDIA       = 'media';
    const WRAP_TYPE   = 'wrap_type';
    const QTY_LIMIT   = 'qty_limit';

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
     * @return int
     */
    public function getPriceType();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setPriceType($value);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setAmount($value);

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
    public function getCategory();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCategory($value);

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

    /**
     * Get media gallery content
     *
     * @return \Magento\Framework\Api\Data\ImageContentInterface|null
     */
    public function getMedia();

    /**
     * Set media gallery content
     *
     * @param \Magento\Framework\Api\Data\ImageContentInterface $value
     *
     * @return $this
     */
    public function setMedia($value);

    /**
     *
     * @return string
     */
    public function getWrapType();

    /**
     *
     * @param string $value
     *
     * @return $this
     */
    public function setWrapType($value);

    /**
     *
     * @return int
     */
    public function getQtyLimit();

    /**
     *
     * @param int $value
     *
     * @return $this
     */
    public function setQtyLimit($value);
}
