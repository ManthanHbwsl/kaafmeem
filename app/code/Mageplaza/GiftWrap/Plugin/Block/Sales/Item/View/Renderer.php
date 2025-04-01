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

namespace Mageplaza\GiftWrap\Plugin\Block\Sales\Item\View;

use Magento\Bundle\Block\Adminhtml\Sales\Order\View\Items\Renderer as ItemRenderer;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class Renderer
 * @package Mageplaza\GiftWrap\Plugin\Block\Sales\Item\View
 */
class Renderer
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Renderer constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param ItemRenderer $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetOrderOptions(ItemRenderer $subject, $result)
    {
        $item = $subject->getItem();

        return $this->helper->getOrderOptions($item, $result);
    }
}
