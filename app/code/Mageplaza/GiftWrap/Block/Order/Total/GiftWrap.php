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

namespace Mageplaza\GiftWrap\Block\Order\Total;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Block\Order;

/**
 * Class GiftWrap
 * @package Mageplaza\GiftWrap\Block\Order\Total
 */
class GiftWrap extends Template
{
    /**
     * @return $this
     */
    public function initTotals()
    {
        /** @var Order\Totals|Order\Invoice\Totals|Order\Creditmemo\Totals $parent */
        $parent = $this->getParentBlock();
        $source = $parent->getSource();

        if ($source->getMpGiftWrapPostcardAmount() > 0.0001) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'mp_gift_wrap_postcard_amount',
                    'value'      => $source->getMpGiftWrapPostcardAmount(),
                    'base_value' => $source->getMpGiftWrapPostcardBaseAmount(),
                    'label'      => __('Postcard')
                ]
            ), 'shipping');
        }

        if ($source->getMpGiftWrapAmount() > 0.0001) {
            $parent->addTotal(new DataObject(
                [
                    'code'       => 'mp_gift_wrap_amount',
                    'value'      => $source->getMpGiftWrapAmount(),
                    'base_value' => $source->getMpGiftWrapBaseAmount(),
                    'label'      => __('Gift Wrap')
                ]
            ), 'shipping');
        }

        return $this;
    }
}
