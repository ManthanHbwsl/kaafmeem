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

namespace Mageplaza\GiftWrap\Model\ResourceModel\Category;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Mageplaza\GiftWrap\Api\Data\CategorySearchResultInterface;
use Mageplaza\GiftWrap\Model\Category;

/**
 * Class Collection
 * @package Mageplaza\GiftWrap\Model\ResourceModel\Category
 */
class Collection extends AbstractCollection implements CategorySearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    protected function _construct()
    {
        $this->_init(Category::class, \Mageplaza\GiftWrap\Model\ResourceModel\Category::class);
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('category_id');
    }

    /**
     * Retrieve option hash
     *
     * @return array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('category_id');
    }
}
