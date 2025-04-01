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

namespace Mageplaza\FreeShippingBar\Api;

/**
 * Interface ShippingBarRepositoryInterface
 * @package Mageplaza\FreeShippingBar\Api
 */
interface ShippingBarRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria The search criteria.
     *
     * @return \Mageplaza\FreeShippingBar\Api\Data\ShippingBarSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param \Mageplaza\FreeShippingBar\Api\Data\ShippingBarInterface $shippingBar
     *
     * @return \Mageplaza\FreeShippingBar\Api\Data\ShippingBarInterface
     */
    public function save(\Mageplaza\FreeShippingBar\Api\Data\ShippingBarInterface $shippingBar);

    /**
     * @param int $id
     * @return bool
     */
    public function delete($id): bool;

    /**
     * @return \Mageplaza\FreeShippingBar\Api\Data\ShippingBarInterface[]
     */
    public function getGuestShippingBars();

    /**
     * @param int $customerId
     *
     * @return \Mageplaza\FreeShippingBar\Api\Data\ShippingBarInterface[]
     */
    public function getCustomerShippingBars($customerId);

    /**
     * @return \Mageplaza\FreeShippingBar\Api\Data\ConfigInterface
     */
    public function getConfig();
}
