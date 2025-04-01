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

use Magento\Framework\Api\ImageContentValidatorInterface;
use Magento\Framework\Api\ImageProcessorInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mageplaza\GiftWrap\Api\Data\WrapInterface;
use Mageplaza\GiftWrap\Api\Data\WrapSearchResultInterfaceFactory;
use Mageplaza\GiftWrap\Api\WrapManagementInterface;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\ResourceModel\Wrap\Collection;
use Mageplaza\GiftWrap\Model\Wrap;
use Mageplaza\GiftWrap\Model\WrapFactory;

/**
 * Class WrapManagement
 * @package Mageplaza\GiftWrap\Model\Api
 */
class WrapManagement extends AbstractManagement implements WrapManagementInterface
{
    /**
     * @var WrapFactory
     */
    private $wrapFactory;

    /**
     * @var WrapSearchResultInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @var ImageContentValidatorInterface
     */
    private $contentValidator;

    /**
     * @var ImageProcessorInterface
     */
    private $imageProcessor;

    /**
     * WrapManagement constructor.
     *
     * @param Data $helper
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TimezoneInterface $timezone
     * @param WrapSearchResultInterfaceFactory $searchResultFactory
     * @param ImageContentValidatorInterface $contentValidator
     * @param ImageProcessorInterface $imageProcessor
     * @param WrapFactory $wrapFactory
     */
    public function __construct(
        Data $helper,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TimezoneInterface $timezone,
        WrapSearchResultInterfaceFactory $searchResultFactory,
        ImageContentValidatorInterface $contentValidator,
        ImageProcessorInterface $imageProcessor,
        WrapFactory $wrapFactory
    ) {
        $this->wrapFactory = $wrapFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->contentValidator = $contentValidator;
        $this->imageProcessor = $imageProcessor;
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
        return $this->getEntity($this->wrapFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->deleteEntity($this->wrapFactory->create(), $id);
    }

    /**
     * {@inheritdoc}
     * @param Wrap $entity
     */
    public function save(WrapInterface $entity)
    {
        $this->checkEnabled();

        $this->uploadMedia($entity);

        $entity->save();

        return $this->get($entity->getId());
    }

    /**
     * @param WrapInterface $entity
     *
     * @throws InputException
     */
    private function uploadMedia($entity)
    {
        $media = $entity->getMedia();

        if (!$media || !$this->contentValidator->isValid($media)) {
            return;
        }

        $relativeFilePath = $this->imageProcessor->processImageContent(Data::TEMPLATE_MEDIA_PATH, $media);

        $entity->setImage(Data::TEMPLATE_MEDIA_PATH . $relativeFilePath);
    }
}
