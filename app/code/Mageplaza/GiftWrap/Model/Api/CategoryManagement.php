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

use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mageplaza\GiftWrap\Api\CategoryManagementInterface;
use Mageplaza\GiftWrap\Api\Data\CategoryInterface;
use Mageplaza\GiftWrap\Api\Data\CategorySearchResultInterfaceFactory;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Category;
use Mageplaza\GiftWrap\Model\CategoryFactory;
use Mageplaza\GiftWrap\Model\ResourceModel\Category\Collection;

/**
 * Class CategoryManagement
 * @package Mageplaza\GiftWrap\Model\Api
 */
class CategoryManagement extends AbstractManagement implements CategoryManagementInterface
{
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var CategorySearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * CategoryManagement constructor.
     *
     * @param Data $helper
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TimezoneInterface $timezone
     * @param CategorySearchResultInterfaceFactory $searchResultFactory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(
        Data $helper,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TimezoneInterface $timezone,
        CategorySearchResultInterfaceFactory $searchResultFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->searchResultFactory = $searchResultFactory;
        parent::__construct($helper, $collectionProcessor, $searchCriteriaBuilder, $timezone);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        /** @var Collection $searchResult */
        $searchResult = $this->searchResultFactory->create();

        return $this->getListEntity($searchResult, $searchCriteria);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->getEntity($this->categoryFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->deleteEntity($this->categoryFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     * @param CategoryInterface|Category $entity
     */
    public function save(CategoryInterface $entity)
    {
        return $this->saveEntity($entity);
    }
}
