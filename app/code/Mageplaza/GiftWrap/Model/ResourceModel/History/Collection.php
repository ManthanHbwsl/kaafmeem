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

namespace Mageplaza\GiftWrap\Model\ResourceModel\History;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Mageplaza\GiftWrap\Api\Data\HistorySearchResultInterface;
use Mageplaza\GiftWrap\Model\History;

/**
 * Class Collection
 * @package Mageplaza\GiftWrap\Model\ResourceModel\History
 */
class Collection extends AbstractCollection implements HistorySearchResultInterface
{
    /**
     * @var string
     */
    protected $_idFieldName = 'history_id';

    protected function _construct()
    {
        $this->_init(History::class, \Mageplaza\GiftWrap\Model\ResourceModel\History::class);
    }
}
