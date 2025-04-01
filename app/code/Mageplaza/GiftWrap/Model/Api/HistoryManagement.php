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
use Mageplaza\GiftWrap\Api\Data\HistorySearchResultInterfaceFactory;
use Mageplaza\GiftWrap\Api\HistoryManagementInterface;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\HistoryFactory;
use Mageplaza\GiftWrap\Model\ResourceModel\History\Collection;

/**
 * Class HistoryManagement
 * @package Mageplaza\GiftWrap\Model\Api
 */
class HistoryManagement extends AbstractManagement implements HistoryManagementInterface
{
    /**
     * @var HistoryFactory
     */
    private $templateFactory;

    /**
     * @var HistorySearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * HistoryManagement constructor.
     *
     * @param Data $helper
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TimezoneInterface $timezone
     * @param HistorySearchResultInterfaceFactory $searchResultFactory
     * @param HistoryFactory $templateFactory
     */
    public function __construct(
        Data $helper,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TimezoneInterface $timezone,
        HistorySearchResultInterfaceFactory $searchResultFactory,
        HistoryFactory $templateFactory
    ) {
        $this->templateFactory = $templateFactory;
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
        return $this->getEntity($this->templateFactory->create(), $id);
    }
}
