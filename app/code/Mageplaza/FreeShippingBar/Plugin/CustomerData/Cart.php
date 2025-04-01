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
 * @package     Mageplaza_FreeShippingBar
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FreeShippingBar\Plugin\CustomerData;

use Mageplaza\FreeShippingBar\Helper\Data as HelperData;

/**
 * Class Cart
 * @package Mageplaza\FreeShippingBar\Plugin\CustomerData
 */
class Cart
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Cart constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * Add Free shipping bar cart total data to result
     *
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, $result)
    {
        if ($this->helperData->isEnabled()) {
            $result['mpFSBCartTotal'] = $this->helperData->getmpFSBCartTotal();
        }

        return $result;
    }
}
