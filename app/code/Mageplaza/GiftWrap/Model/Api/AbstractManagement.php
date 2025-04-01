<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license sliderConfig is
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

namespace Mageplaza\GiftWrap\Model\Api;

use Exception;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Category;
use Mageplaza\GiftWrap\Model\History;
use Mageplaza\GiftWrap\Model\ResourceModel\Category\Collection as CategoryCollection;
use Mageplaza\GiftWrap\Model\ResourceModel\History\Collection as HistoryCollection;
use Mageplaza\GiftWrap\Model\ResourceModel\Wrap\Collection as WrapCollection;
use Mageplaza\GiftWrap\Model\Wrap;

/**
 * Class AbstractManagement
 * @package Mageplaza\GiftWrap\Model\Api
 */
class AbstractManagement
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * AbstractManagement constructor.
     *
     * @param Data $helper
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Data $helper,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TimezoneInterface $timezone
    ) {
        $this->helper = $helper;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->timezone = $timezone;
    }

    /**
     * @param CategoryCollection|WrapCollection|HistoryCollection $searchResult
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return CategoryCollection|WrapCollection|HistoryCollection
     * @throws LocalizedException
     */
    public function getListEntity($searchResult, $searchCriteria)
    {
        $this->checkEnabled();

        if ($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        $this->collectionProcessor->process($searchCriteria, $searchResult);
        if ($searchResult instanceof CategoryCollection || $searchResult instanceof WrapCollection) {
            foreach ($searchResult->getItems() as $item) {
                /** @var Category|Wrap $item */
                $this->convertDateTime($item, 'created_at');
                $this->convertDateTime($item, 'updated_at');
            }
        } else {
            foreach ($searchResult->getItems() as $item) {
                /** @var History $item */
                $this->convertDateTime($item, 'order_date');
            }
        }

        return $searchResult->setSearchCriteria($searchCriteria);
    }

    /**
     * @param Category|Wrap|History $entity
     * @param int $id
     *
     * @return Category|Wrap|History
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getEntity($entity, $id)
    {
        $this->checkEnabled();

        $entity->load($id);

        if (!$entity->getId()) {
            throw new NoSuchEntityException(__("The entity that was requested doesn't exist. Verify the entity and try again."));
        }

        if ($entity instanceof Category || $entity instanceof Wrap) {
            $this->convertDateTime($entity, 'created_at');
            $this->convertDateTime($entity, 'updated_at');
        } else {
            $this->convertDateTime($entity, 'order_date');
        }

        return $entity;
    }

    /**
     * @param Category|Wrap|History $entity
     * @param string $field
     */
    private function convertDateTime($entity, $field)
    {
        $entity->setData($field, $this->timezone->date($entity->getData($field))->format('Y-m-d H:i:s'));
    }

    /**
     * @param Wrap|Category $entity
     * @param int $id
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function deleteEntity($entity, $id)
    {
        $this->checkEnabled();

        $entity->load($id);

        if (!$entity->getId()) {
            throw new NoSuchEntityException(__("The entity that was requested doesn't exist. Verify the entity and try again."));
        }

        $entity->delete();

        return true;
    }

    /**
     * @param Category|Wrap $entity
     *
     * @return Category|Wrap
     * @throws Exception
     */
    public function saveEntity($entity)
    {
        $this->checkEnabled();

        $entity->save();

        return $this->get($entity->getId());
    }

    /**
     * @throws LocalizedException
     */
    protected function checkEnabled()
    {
        if (!$this->helper->isEnabled()) {
            throw new LocalizedException(__('The module is disabled'));
        }
    }
}
