<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model;

use BrainActs\SalesRepresentative\Api\Data;
use BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use BrainActs\SalesRepresentative\Model\ResourceModel\Withdrawals as ResourceWithdrawals;
use BrainActs\SalesRepresentative\Model\ResourceModel\Withdrawals\CollectionFactory as WithdrawalsCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class WithdrawalsRepository
 *
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class WithdrawalsRepository implements WithdrawalsRepositoryInterface
{
    /**
     * @var ResourceWithdrawals
     */
    public $resource;

    /**
     * @var WithdrawalsFactory
     */
    public $withdrawalFactory;

    /**
     * @var WithdrawalsCollectionFactory
     */
    public $withdrawalCollectionFactory;

    /**
     * @var Data\WithdrawalsSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * @var \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterfaceFactory
     */
    public $dataWithdrawalsFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @param \BrainActs\SalesRepresentative\Model\ResourceModel\Withdrawals $resource
     * @param \BrainActs\SalesRepresentative\Model\WithdrawalsFactory $withdrawalFactory
     * @param \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterfaceFactory $dataWithdrawalsFactory
     * @param \BrainActs\SalesRepresentative\Model\ResourceModel\Withdrawals\CollectionFactory $withdrawalCollectionFactory
     * @param \BrainActs\SalesRepresentative\Api\Data\WithdrawalsSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        ResourceWithdrawals $resource,
        WithdrawalsFactory $withdrawalFactory,
        \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterfaceFactory $dataWithdrawalsFactory,
        WithdrawalsCollectionFactory $withdrawalCollectionFactory,
        Data\WithdrawalsSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->resource = $resource;
        $this->withdrawalFactory = $withdrawalFactory;
        $this->withdrawalCollectionFactory = $withdrawalCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataWithdrawalsFactory = $dataWithdrawalsFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface $withdrawal
     * @return \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(Data\WithdrawalsInterface $withdrawal)
    {
        try {
            $this->resource->save($withdrawal);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $withdrawal;
    }

    /**
     * @param int $withdrawalId
     * @return \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface|\BrainActs\SalesRepresentative\Model\Withdrawals
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($withdrawalId)
    {
        $withdrawal = $this->withdrawalFactory->create();
        $this->resource->load($withdrawal, $withdrawalId);
        if (!$withdrawal->getId()) {
            throw new NoSuchEntityException(__('Withdrawals with id "%1" does not exist.', $withdrawalId));
        }
        return $withdrawal;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteriaItem
     * @return \BrainActs\SalesRepresentative\Api\Data\WithdrawalsSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteriaItem)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteriaItem);

        $collection = $this->withdrawalCollectionFactory->create();
        foreach ($criteriaItem->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteriaItem->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteriaItem->getCurrentPage());
        $collection->setPageSize($criteriaItem->getPageSize());
        $withdrawals = [];
        /** @var Withdrawals $withdrawalModel */
        foreach ($collection as $withdrawalModel) {
            $withdrawalData = $this->dataWithdrawalsFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $withdrawalData,
                $withdrawalModel->getData(),
                Data\WithdrawalsInterface::class
            );
            $withdrawals[] = $this->dataObjectProcessor->buildOutputDataArray(
                $withdrawalData,
                Data\WithdrawalsInterface::class
            );
        }
        $searchResults->setItems($withdrawals);
        return $searchResults;
    }

    /**
     * @param \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface $withdrawal
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(Data\WithdrawalsInterface $withdrawal)
    {
        try {
            $this->resource->delete($withdrawal);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * @param int $withdrawalId
     * @return bool
     */
    public function deleteById($withdrawalId)
    {
        return $this->delete($this->getById($withdrawalId));
    }
}
