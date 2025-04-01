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

namespace Mageplaza\GiftWrap\Plugin\Block\Sales\Item\Renderer;

use Magento\Sales\Model\Order\Item;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class DefaultRenderer
 * @package Mageplaza\GiftWrap\Plugin\Block\Sales\Item\Renderer
 */
class DefaultRenderer
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * DefaultRenderer constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetItemOptions(\Magento\Sales\Block\Order\Item\Renderer\DefaultRenderer $subject, $result)
    {
        /** @var Item $item */
        $item = $subject->getOrderItem();

        return $this->helper->getOrderOptions($item, $result);
    }
}
