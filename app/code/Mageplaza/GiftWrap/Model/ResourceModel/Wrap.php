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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\GiftWrap\Model\Config\Source\Price;
use Mageplaza\GiftWrap\Model\Config\Source\Status;
use Mageplaza\GiftWrap\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class Wrap
 * @package Mageplaza\GiftWrap\Model\ResourceModel
 */
class Wrap extends AbstractDb
{
    /**
     * Date model
     *
     * @var DateTime
     */
    private $date;

    /**
     * @var Category\CollectionFactory
     */
    private $collectionFactory;

    /**
     * Attribute constructor.
     *
     * @param Context $context
     * @param DateTime $date
     * @param Category\CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        DateTime $date,
        CollectionFactory $collectionFactory
    ) {
        $this->date = $date;
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_giftwrap_wrap', 'wrap_id');
    }

    /**
     * Before save callback
     *
     * @param AbstractModel|\Mageplaza\GiftWrap\Model\Wrap $object
     *
     * @return $this|AbstractDb
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->getName() === '') {
            throw new LocalizedException(__('"name" must not be empty'));
        }

        $price = $object->getPriceType();
        if ($price && !isset(Price::getOptionArray()[$price])) {
            throw new LocalizedException(__('"price type" must be either 1 (Fixed) or 2 (By Qty)'));
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

        if (!$object->getAmount()) {
            $object->setAmount(0);
        } elseif (!is_numeric($object->getAmount()) || $object->getAmount() < 0) {
            throw new LocalizedException(__('"amount" must be a valid number'));
        }

        $categories = $this->collectionFactory->create()->toOptionArray();
        $this->isObjectsExist('category', $object->getCategory(), array_column($categories, 'value'));

        $object->setUpdatedAt($this->date->date());

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
