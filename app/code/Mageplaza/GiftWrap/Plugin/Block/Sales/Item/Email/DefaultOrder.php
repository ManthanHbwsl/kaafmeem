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

namespace Mageplaza\GiftWrap\Plugin\Block\Sales\Item\Email;

use Magento\Sales\Model\Order\Item;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class DefaultOrder
 * @package Mageplaza\GiftWrap\Plugin\Block\Sales\Item\Email
 */
class DefaultOrder
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * DefaultOrder constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetItemOptions(\Magento\Sales\Block\Order\Email\Items\Order\DefaultOrder $subject, $result)
    {
        /** @var Item $item */
        $item = $subject->getItem();

        return $this->helper->getOrderOptions($item, $result);
    }
}
