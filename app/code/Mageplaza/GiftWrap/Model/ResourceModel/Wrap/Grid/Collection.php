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

namespace Mageplaza\GiftWrap\Model\ResourceModel\Wrap\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Mageplaza\GiftWrap\Model\ResourceModel\Wrap\Grid
 */
class Collection extends SearchResult
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'wrap_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_giftwrap_wrap_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'giftwrap_wrap_collection';

    /**
     * @return $this|Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
            ['giftwrap_history' => $this->getTable('mageplaza_giftwrap_history')],
            'main_table.wrap_id = giftwrap_history.wrap_id',
            []
        )->columns([
            'quantity' => new Zend_Db_Expr(
                'SUM(IF(giftwrap_history.mp_gift_wrap_invoice_base_amount IS NULL, 0 , 1))'
            ),
            'revenue'  => new Zend_Db_Expr(
                'SUM(
                IFNULL(giftwrap_history.mp_gift_wrap_invoice_base_amount, 0)
                - IFNULL(giftwrap_history.mp_gift_wrap_creditmemo_base_amount, 0)
                )'
            ),
        ])->group('main_table.wrap_id');

        $this->addFilterToMap('wrap_id', 'main_table.wrap_id');

        return $this;
    }
}
