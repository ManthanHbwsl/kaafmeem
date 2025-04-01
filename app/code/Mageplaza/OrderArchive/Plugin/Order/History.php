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

namespace Mageplaza\OrderArchive\Plugin\Order;

use Magento\Sales\Block\Order\History as OrderHistory;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Mageplaza\OrderArchive\Helper\Data as HelperData;

/**
 * Class History
 *
 * @package Mageplaza\OrderArchive\Plugin\Order
 */
class History
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * History constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->_helperData = $helperData;
    }

    /**
     * @param OrderHistory $subject
     * @param bool|Collection $result
     *
     * @return bool|Collection
     * @SuppressWarnings(Unused)
     */
    public function afterGetOrders(OrderHistory $subject, $result)
    {
        if (!$this->_helperData->isShowArchive()) {
            /** @var Collection $result */
            $result->getSelect()->where('is_archive = 0 OR is_archive IS NULL');
        }

        return $result;
    }
}
