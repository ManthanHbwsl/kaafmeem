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

namespace Mageplaza\GiftWrap\Model\Sales\Pdf;

use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory;

/**
 * Class GiftWrap
 * @package Mageplaza\GiftWrap\Model\Sales\Pdf
 */
class GiftWrap extends DefaultTotal
{
    /**
     * @var \Mageplaza\GiftWrap\Helper\Data
     */
    private $helper;

    /**
     * GiftWrap constructor.
     *
     * @param Data $taxHelper
     * @param Calculation $taxCalculation
     * @param CollectionFactory $ordersFactory
     * @param \Mageplaza\GiftWrap\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        Data $taxHelper,
        Calculation $taxCalculation,
        CollectionFactory $ordersFactory,
        \Mageplaza\GiftWrap\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $total = [];

        if (abs($this->getAmount()) > 0) {
            $total = [
                'amount' => $this->helper->formatPrice($this->getAmount(), false),
                'label' => __('Gift Wrap'),
                'font_size' => $this->getFontSize() ?: 7
            ];
        }

        return [$total];
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->getSource()->getMpGiftWrapAmount();
    }
}
