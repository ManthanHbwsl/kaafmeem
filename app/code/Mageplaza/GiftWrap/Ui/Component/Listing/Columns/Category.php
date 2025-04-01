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

namespace Mageplaza\GiftWrap\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mageplaza\GiftWrap\Model\CategoryFactory;
use Mageplaza\GiftWrap\Model\ResourceModel\Category\Collection;

/**
 * Class Category
 * @package Mageplaza\GiftWrap\Ui\Component\Listing\Columns
 */
class Category extends Column
{
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * Category constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CategoryFactory $categoryFactory
     * @param Collection $collection
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategoryFactory $categoryFactory,
        Collection $collection,
        array $components = [],
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->collection = $collection;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get customer group name
     *
     * @param array $item
     *
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $content = [];

        if (isset($item['category'])) {
            $cateIds = explode(',', $item['category']);
            foreach ($cateIds as $cateId) {
                /** @var \Mageplaza\GiftWrap\Model\Category $category */
                $category = $this->categoryFactory->create()->load($cateId);

                $content[] = $category->getName() ?: $cateId;
            }
        }

        return implode(', ', $content);
    }
}
