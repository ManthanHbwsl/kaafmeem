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
 * @category  Mageplaza
 * @package   Mageplaza_OrderArchive
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderArchive\Model\ResourceModel\Archive\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderCollection;

/**
 * Class Collection
 *
 * @package Mageplaza\OrderArchive\Model\ResourceModel\Archive\Grid
 */
class Collection extends OrderCollection
{
    /**
     * @return OrderCollection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->where('main_table.is_archive = 1');
    }
}
