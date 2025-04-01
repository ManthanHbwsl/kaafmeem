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

namespace Mageplaza\GiftWrap\Model\ResourceModel;

use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\System\Store;
use Mageplaza\GiftWrap\Model\Config\Source\Status;

/**
 * Class Category
 * @package Mageplaza\GiftWrap\Model\ResourceModel
 */
class Category extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    private $date;

    /**
     * @var Store
     */
    private $systemStore;

    /**
     * @var Collection
     */
    private $groupCollection;

    /**
     * Attribute constructor.
     *
     * @param Context $context
     * @param DateTime $date
     * @param Store $systemStore
     * @param Collection $groupCollection
     */
    public function __construct(
        Context $context,
        DateTime $date,
        Store $systemStore,
        Collection $groupCollection
    ) {
        $this->date = $date;
        $this->systemStore = $systemStore;
        $this->groupCollection = $groupCollection;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_giftwrap_category', 'category_id');
    }

    /**
     * Before save callback
     *
     * @param AbstractModel|\Mageplaza\GiftWrap\Model\Category $object
     *
     * @return $this|AbstractDb
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->getName() === '') {
            throw new LocalizedException(__('"name" must not be empty'));
        }

        if (!$object->getStatus()) {
            $object->setStatus(0);
        } elseif (!isset(Status::getOptionArray()[$object->getStatus()])) {
            throw new LocalizedException(__('"status" must be either 1 (Enabled) or 2 (Disabled)'));
        }

        if (!$object->getSortOrder()) {
            $object->setSortOrder(0);
        } elseif (!is_numeric($object->getSortOrder()) || $object->getSortOrder() < 0) {
            throw new LocalizedException(__('"sort order" must be a valid number'));
        }

        if ($storeId = $object->getStoreId()) {
            $this->isObjectsExist('store_id', $storeId, $this->systemStore->getStoreOptionHash(true, 'id'));
        } else {
            $object->setStoreId(0);
        }

        $object->setUpdatedAt($this->date->date());

        $this->isObjectsExist('customer_group', $object->getCustomerGroup(), $this->groupCollection->getAllIds());

        return $this;
    }

    /**
     * @param string $field
     * @param string $data
     * @param array $allObjectIds
     *
     * @throws NoSuchEntityException
     */
    private function isObjectsExist($field, $data, $allObjectIds)
    {
        if (!$data) {
            return;
        }

        $diffGroups = array_diff(explode(',', $data), $allObjectIds);

        if (count($diffGroups)) {
            throw NoSuchEntityException::singleField($field, implode(', ', $diffGroups));
        }
    }
}
