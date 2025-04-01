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

namespace Mageplaza\GiftWrap\Plugin\Block\Sales\Item\Column;

use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class DefaultColumn
 * @package Mageplaza\GiftWrap\Plugin\Block\Sales\Item\Column
 */
class DefaultColumn
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * DefaultColumn constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetOrderOptions(\Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn $subject, $result)
    {
        $item = $subject->getItem();

        return $this->helper->getOrderOptions($item, $result);
    }
}
