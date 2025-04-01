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

namespace Mageplaza\FreeShippingBar\Model\Api;

use Exception;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTime as Date;
use Mageplaza\FreeShippingBar\Api\Data\ShippingBarInterface;
use Mageplaza\FreeShippingBar\Api\Data\ShippingBarSearchResultsInterface;
use Mageplaza\FreeShippingBar\Api\ShippingBarRepositoryInterface;
use Mageplaza\FreeShippingBar\Helper\Data;
use Mageplaza\FreeShippingBar\Helper\Image;
use Mageplaza\FreeShippingBar\Model\ResourceModel\Shippingbar;
use Mageplaza\FreeShippingBar\Model\ResourceModel\Shippingbar\Collection;
use Mageplaza\FreeShippingBar\Model\ResourceModel\Shippingbar\CollectionFactory;
use Mageplaza\FreeShippingBar\Model\Shippingbar as ShippingBarModel;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Allowshow;
use Mageplaza\FreeShippingBar\Model\Shippingbar\FontText;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Position;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Templates;
use Mageplaza\FreeShippingBar\Model\ShippingbarFactory;

/**
 * Class ShippingBarRepository
 * @package Mageplaza\FreeShippingBar\Model\Api
 */
class ShippingBarRepository implements ShippingBarRepositoryInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ShippingbarFactory
     */
    private $shippingBarFactory;

    /**
     * @var Shippingbar
     */
    private $shippingBarResource;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Date
     */
    private $date;

    /**
     * @var Image
     */
    private $imageHelper;

    /**
     * @var FontText
     */
    private $fontText;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var Templates
     */
    private $templatesSource;

    /**
     * @var Position
     */
    private $positionSource;

    /**
     * ShippingBarRepository constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param Data $helperData
     * @param ShippingbarFactory $shippingBarFactory
     * @param Shippingbar $shippingBarResource
     * @param DateTime $dateTime
     * @param Date $date
     * @param Image $imageHelper
     * @param FontText $fontText
     * @param CustomerRepository $customerRepository
     * @param Templates $templatesSource
     * @param Position $positionSource
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionProcessorInterface $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultsFactory,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        Data $helperData,
        ShippingbarFactory $shippingBarFactory,
        Shippingbar $shippingBarResource,
        DateTime $dateTime,
        Date $date,
        Image $imageHelper,
        FontText $fontText,
        CustomerRepository $customerRepository,
        Templates $templatesSource,
        Position $positionSource
    ) {
        $this->helperData = $helperData;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->request = $request;
        $this->collectionFactory = $collectionFactory;
        $this->shippingBarFactory = $shippingBarFactory;
        $this->shippingBarResource = $shippingBarResource;
        $this->dateTime = $dateTime;
        $this->date = $date;
        $this->imageHelper = $imageHelper;
        $this->fontText = $fontText;
        $this->customerRepository = $customerRepository;
        $this->templatesSource = $templatesSource;
        $this->positionSource = $positionSource;
    }

    /**
     * @throws LocalizedException
     */
    public function checkEnabled()
    {
        if (!$this->helperData->isEnabled()) {
            throw new LocalizedException(__('The module is disabled'));
        }
    }

    /**
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return ShippingBarSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null)
    {
        $this->checkEnabled();
        if ($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);

        foreach ($collection->getItems() as $item) {
            $item->setImageBackgroundUrl(
                $this->helperData->getImageBackgroundUrl($item->getImage(), $item->getTemplate())
            );
        }

        /** @var ShippingBarSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());

        return $searchResult;
    }

    /**
     * @param ShippingBarInterface $shippingBar
     *
     * @return ShippingBarInterface
     * @throws LocalizedException
     * @throws InputException
     */
    public function save(ShippingBarInterface $shippingBar)
    {
        $this->checkEnabled();
        $shippingBarId = $shippingBar->getRuleId();
        /** @var ShippingBarModel $shippingBarModel */
        $shippingBarModel = $this->shippingBarFactory->create();
        if ($shippingBarId) {
            $this->shippingBarResource->load($shippingBarModel, $shippingBar->getRuleId());

            if (!$shippingBarModel->getId()) {
                throw new NoSuchEntityException();
            }
        }

        $modelData = $shippingBarModel->getData();
        $data = $shippingBar->getData();
        $mergedData = array_merge($modelData, $data);
        $this->validateData($mergedData, $shippingBarModel);
        $shippingBarModel->addData($mergedData);

        $this->shippingBarResource->save($shippingBarModel);
        $this->shippingBarResource->load($shippingBarModel, $shippingBarModel->getId());

        return $shippingBarModel;
    }

    /**
     * @param array $data
     * @param ShippingBarModel $shippingBarModel
     *
     * @throws InputException
     */
    public function validateData(&$data, $shippingBarModel)
    {
        $requiredFields = [
            ShippingBarInterface::NAME,
            ShippingBarInterface::STORE_ID,
            ShippingBarInterface::CUSTOMER_GROUP_ID,
            ShippingBarInterface::GOAL,
            ShippingBarInterface::FIRST_MESSAGE,
            ShippingBarInterface::BELOW_GOAL_MESSAGE,
            ShippingBarInterface::ACHIEVE_MESSAGE,
            ShippingBarInterface::BAR_BACKGROUND,
            ShippingBarInterface::BAR_TEXT_COLOR,
            ShippingBarInterface::BAR_LINK_COLOR,
            ShippingBarInterface::GOAL_TEXT_COLOR,
            ShippingBarInterface::FONT_SIZE,
            ShippingBarInterface::FONT_TEXT,
            ShippingBarInterface::STATUS,
            ShippingBarInterface::POSITION,
        ];
        if (isset($data[ShippingBarInterface::CLICK_ABLE]) && $data[ShippingBarInterface::CLICK_ABLE]) {
            $data[ShippingBarInterface::CLICK_ABLE] = (bool)$data[ShippingBarInterface::CLICK_ABLE];
            $requiredFields[] = ShippingBarInterface::URL_SHIPPING_BAR;
        }

        if ($data[ShippingBarInterface::POSITION] !== Position::INSERT_SNIPPET) {
            $requiredFields[] = ShippingBarInterface::ALLOW_SHOW;
        }

        $missingFields = [];
        foreach ($requiredFields as $requiredField) {
            if (!isset($data[$requiredField])
                || (!$data[$requiredField]
                    && $requiredField !== 'store_id'
                    && $requiredField !== 'customer_group_id')) {
                $missingFields[] = $requiredField;
            }
        }

        if (!empty($missingFields)) {
            throw new InputException(__('Please specific Shipping Bar field(s): %1', implode(',', $missingFields)));
        }

        if (isset($data['bar_opacity']) && $data['bar_opacity'] > 1) {
            $data['bar_opacity'] = 1;
        }

        if ($data['goal'] < 0) {
            throw new InputException(__('Goal must be a number greater than 0'));
        }
        $data['string_font_connect'] = str_replace(' ', '+', $data['font_text']);
        $data['to_date'] = (!isset($data['to_date']) || $data['to_date'] === '')
            ? null : $this->dateTime->formatDate($data['to_date']);
        $data['from_date'] = (!isset($data['from_date']) || $data['from_date'] === '')
            ? $this->date->date()
            : $this->dateTime->formatDate($data['from_date']);

        unset($data['updated_at'], $data['created_at']);

        if ($this->request->getParam('image')) {
            $data['image'] = '';
        } else {
            try {
                $this->imageHelper->uploadImage(
                    $data,
                    'image',
                    Image::TEMPLATE_MEDIA_TYPE_BACKGROUND,
                    $shippingBarModel->getImage()
                );
            } catch (Exception $exception) {
                $data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
            }
        }

        $validFonts = $this->fontText->toArray();

        if (!isset($data['font_text']) || !in_array($data['font_text'], $validFonts, true)) {
            throw new InputException(__(
                'Please specific font_text field. Valid font must be one of fonts: %1',
                implode(',', $validFonts)
            ));
        }

        $data['string_font_connect'] = str_replace(' ', '+', $data['font_text']);
        $data['status'] = (bool)$data['status'];

        if (isset($data['priority'])) {
            $data['priority'] = abs((int)$data['priority']);
        }

        if (isset($data['template'])) {
            $templateIds = array_keys($this->templatesSource->getTemplateModelArray());
            if (!in_array($data['template'], $templateIds, false)) {
                throw new InputException(__(
                    'Please specific template, it must one of values: %1',
                    implode(',', $templateIds)
                ));
            }
        }

        $positions = $this->positionSource->toArray();

        if (!in_array($data[ShippingBarInterface::POSITION], $positions, true)) {
            throw new InputException(__(
                'Please specific position field. Valid position be one of values: %1',
                implode(',', $positions)
            ));
        }

        if (isset($data[ShippingBarInterface::ALLOW_SHOW])) {
            $allowShows = [Allowshow::ALL_PAGES, Allowshow::SPECIFIC_PAGES];
            if (!in_array($data[ShippingBarInterface::ALLOW_SHOW], $allowShows, true)) {
                throw new InputException(__(
                    'Please specific allowshow field. Valid allow_show be one of values: %1',
                    implode(',', $allowShows)
                ));
            }
        }
    }

    /**
     * @param int $id
     *
     * @return bool
     * @throws Exception
     */
    public function delete($id): bool
    {
        $this->checkEnabled();
        $shippingBar = $this->shippingBarFactory->create();
        $this->shippingBarResource->load($shippingBar, $id);
        if (!$shippingBar->getId()) {
            throw new LocalizedException(__('No such entity ID'));
        }

        $this->shippingBarResource->delete($shippingBar);

        return true;
    }

    /**
     * @param int $customerGroupId
     *
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getShippingBars($customerGroupId)
    {
        $collection = $this->collectionFactory->create();
        $storeId = $this->helperData->getStoreId();
        $collection->addActiveFilter()
            ->addDateFilter()
            ->addCustomerGroupFilter($customerGroupId)
            ->addStoreFilter($storeId);

        return $collection;
    }

    /**
     * @param Collection $collection
     * @param string|array $position
     * @param string $fullActionName
     * @param string $currentPath
     *
     * @return ShippingBarModel[]
     */
    public function getShippingBarByPosition($collection, $position, $fullActionName, $currentPath)
    {
        $collectionByPosition = (clone $collection)->addFieldToFilter('position', $position);

        $allPageShippingBarPriority = [];
        $allPageShippingBar = [];
        $specificPageShippingBarPriority = [];
        $specificPageShippingBar = [];

        /** @var ShippingBarModel $item */
        foreach ($collectionByPosition as $item) {
            if ($item->checkExclude($fullActionName, $currentPath)) {
                continue;
            }
            $itemPosition = $item->getPosition();
            if ($item->getAllowshow() === Allowshow::ALL_PAGES) {
                if (!isset($allPageShippingBarPriority[$itemPosition])) {
                    $allPageShippingBarPriority[$itemPosition] = $item->getPriority();
                }

                if ($item->getPriority() <= $allPageShippingBarPriority[$itemPosition]) {
                    $allPageShippingBar[$itemPosition] = $item;
                }
            } else {
                if (!$item->checkInclude($fullActionName, $currentPath)) {
                    continue;
                }
                if (!isset($specificPageShippingBarPriority[$itemPosition])) {
                    $specificPageShippingBarPriority[$itemPosition] = $item->getPriority();
                }

                if ($item->getPriority() <= $specificPageShippingBarPriority[$itemPosition]) {
                    $specificPageShippingBar[$itemPosition] = $item;
                }
            }
        }

        return array_filter(array_merge(array_values($allPageShippingBar), array_values($specificPageShippingBar)));
    }

    /**
     * @return ShippingBarModel[]
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getGuestShippingBars()
    {
        $this->checkEnabled();

        return $this->getShippingBarsByCustomerGroup(0);
    }

    /**
     * @param int $customerId
     *
     * @return ShippingBarModel[]
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCustomerShippingBars($customerId)
    {
        $this->checkEnabled();
        $customer = $this->customerRepository->getById($customerId);
        $customerGroupId = $customer->getGroupId();

        return $this->getShippingBarsByCustomerGroup($customerGroupId);
    }

    /**
     * @param int $customerGroupId
     *
     * @return ShippingBarModel[]
     * @throws NoSuchEntityException
     */
    protected function getShippingBarsByCustomerGroup($customerGroupId)
    {
        $fullActionName = $this->request->getParam('full_action_name');
        $currentPath = $this->request->getParam('current_path');
        $collection = $this->getShippingBars($customerGroupId);

        $position = [
            'in' => [
                Position::TOP_CONTENT,
                Position::BOTTOM_FIXED_BAR,
                Position::TOP_FIXED_BAR,
                Position::TOP_PAGE
            ]
        ];

        $shippingBars = $this->getShippingBarByPosition($collection, $position, $fullActionName, $currentPath);

        foreach ($shippingBars as $shippingBar) {
            $img = $this->helperData->getImageBackgroundUrl($shippingBar->getImage(), $shippingBar->getTemplate());
            $shippingBar->setImageBackgroundUrl($img);
            $shippingBar->setImage($img);
        }

        return $shippingBars;
    }

    /**
     * @return DataObject
     */
    public function getConfig()
    {
        $formatPrice = new DataObject($this->helperData->getPriceFormat());

        return new DataObject([
            'price_format' => $formatPrice
        ]);
    }
}
