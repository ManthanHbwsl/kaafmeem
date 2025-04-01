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

namespace Mageplaza\GiftWrap\Model\ResourceModel\Category\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Class Collection
 * @package Mageplaza\GiftWrap\Model\ResourceModel\Category\Grid
 */
class Collection extends SearchResult
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_giftwrap_category_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'giftwrap_category_collection';

    /**
     * @param string $field
     * @param null $condition
     *
     * @return SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'customer_group') {
            $condition['like'] = "%{$condition['eq']}%";
            unset($condition['eq']);
        }

        if ($field === 'store_id') {
            $condition = ['finset' => $condition['eq']];
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
