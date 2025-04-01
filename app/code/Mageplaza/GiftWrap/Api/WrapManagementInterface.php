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

namespace Mageplaza\GiftWrap\Api;

/**
 * Interface WrapManagementInterface
 * @package Mageplaza\GiftWrap\Api
 */
interface WrapManagementInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     *
     * @return \Mageplaza\GiftWrap\Api\Data\WrapSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param string $id
     *
     * @return \Mageplaza\GiftWrap\Api\Data\WrapInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * @param string $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function delete($id);

    /**
     * @param \Mageplaza\GiftWrap\Api\Data\WrapInterface $entity
     *
     * @return \Mageplaza\GiftWrap\Api\Data\WrapInterface
     * @throws \Exception
     */
    public function save(\Mageplaza\GiftWrap\Api\Data\WrapInterface $entity);
}
