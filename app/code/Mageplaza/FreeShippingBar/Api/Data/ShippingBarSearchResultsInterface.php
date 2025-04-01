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

namespace Mageplaza\FreeShippingBar\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ShippingBarSearchResultsInterface
 * @package Mageplaza\FreeShippingBar\Api\Data
 */
interface ShippingBarSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Mageplaza\FreeShippingBar\Api\Data\ShippingBarInterface[]
     */
    public function getItems();

    /**
     * @param \Mageplaza\FreeShippingBar\Api\Data\ShippingBarInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items = null);
}
