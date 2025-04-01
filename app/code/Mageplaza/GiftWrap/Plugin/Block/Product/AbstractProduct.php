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

namespace Mageplaza\GiftWrap\Plugin\Block\Product;

use Closure;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class AbstractProduct
 *
 * @package Mageplaza\GiftWrap\Plugin\Block\Product
 */
class AbstractProduct
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * AbstractProduct constructor.
     *
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @param \Magento\Catalog\Block\Product\AbstractProduct $subject
     * @param Closure $proceed
     * @param string $name
     *
     * @return string
     * @SuppressWarnings(Unused)
     */
    public function aroundGetBlockHtml(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        Closure $proceed,
        $name
    ) {
        $html    = $proceed($name);
        $product = $subject->getProduct();
        if (!$product || !$this->data->isEnabled() || !in_array($product->getTypeId(), Data::ALLOWED_TYPE, true)) {
            return $html;
        }

        if ($name === 'formkey') {
            $html .= '<input type="hidden" name="mp_gift_wrap_data" value/>
                      <input type="hidden" name="mp_gift_wrap_postcard_data" value/>';
        }

        return $html;
    }
}
