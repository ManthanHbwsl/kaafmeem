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

namespace Mageplaza\GiftWrap\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as Wrap;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Category;
use Mageplaza\GiftWrap\Model\CategoryFactory;
use Mageplaza\GiftWrap\Model\HistoryFactory;
use Mageplaza\GiftWrap\Model\Config\Source\WrapType;

/**
 * Class QuoteSubmitSuccess
 * @package Mageplaza\GiftWrap\Observer
 */
class QuoteSubmitSuccess implements ObserverInterface
{
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var Data
     */
    protected $data;

    /**
     * QuoteSubmitSuccess constructor.
     *
     * @param CategoryFactory $categoryFactory
     * @param HistoryFactory $historyFactory
     * @param Data $data
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        HistoryFactory $historyFactory,
        Data $data
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->historyFactory  = $historyFactory;
        $this->data            = $data;
    }

    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order   = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();
        if (!$this->data->isEnabled($storeId)) {
            return;
        }

        $wraps   = [];
        $orderId = $order->getId();
        $incrId  = $order->getIncrementId();

        foreach ($order->getAllItems() as $item) {
            $wrapData     = $this->data->jsonDecodeData($item->getData(Data::GIFT_WRAP_DATA));
            $postcardData = $this->data->jsonDecodeData($item->getData(Data::GIFT_WRAP_POSTCARD_DATA));
            if (!empty($wrapData)
                && (empty($wrapData['all_cart']) || $this->data->isShowAllCart($storeId))
                && (!empty($wrapData['all_cart']) || $this->data->isShowOnProduct($storeId))) {
                $this->setWraps($wraps, $item, $wrapData, $orderId, $incrId);
            }
            if (!empty($postcardData)
                && (empty($postcardData['all_cart']) || $this->data->isShowAllCart($storeId))
                && (!empty($postcardData['all_cart']) || $this->data->isShowOnProduct($storeId))) {
                $this->setWraps($wraps, $item, $postcardData, $orderId, $incrId);
            }
        }

        foreach ($wraps as &$wrap) {
            $wrap['product_list'] = $this->data->jsonEncodeData($wrap['product_list']);
            $this->historyFactory->create()
                ->setData($wrap)
                ->save();
        }
    }

    /**
     * @param array $wraps
     * @param Item $item
     * @param array|mixed $data
     * @param int $orderId
     * @param string $incrId
     */
    private function setWraps(&$wraps, $item, $data, $orderId, $incrId)
    {
        $identifier = empty($data[Wrap::ALL_CART])
            ? $item->getId() . '_' . $data[Wrap::WRAP_TYPE] . '_item'
            : $data[Wrap::WRAP_ID] . '_cart';

        $wraps[$identifier]['wrap_id']  = $data[Wrap::WRAP_ID];
        $wraps[$identifier]['wrap']     = $data[Wrap::NAME];
        $wraps[$identifier]['category'] = $this->getCategories($data);
        $wraps[$identifier]['image']    = $data[Wrap::IMAGE];
        $wraps[$identifier]['order_id'] = $orderId;
        $wraps[$identifier]['incr_id']  = $incrId;
        $wraps[$identifier]['message']  = $this->getGiftMessage($data);

        if (empty($wraps[$identifier]['product_list'])) {
            $wraps[$identifier]['product_list'] = [];
        }

        $name = $item->getName();

        if (is_string($simpleName = $item->getProductOptionByCode('simple_name'))) {
            $name = $simpleName;
        }

        $wraps[$identifier]['product_list'][] = [
            'id'   => $item->getId(),
            'name' => $name,
            'qty'  => (float)$item->getQtyOrdered()
        ];
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function getCategories($data)
    {
        $categories = [];

        foreach (explode(',', $data['category']) as $cateId) {
            /** @var Category $category */
            $category = $this->categoryFactory->create()->load($cateId);

            $categories[] = $category->getName();
        }

        return implode(', ', $categories);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function getGiftMessage($data)
    {
        if ($data[Wrap::WRAP_TYPE] === WrapType::GIFT_WRAP_POST_CARD_TYPE) {
            return $data[Wrap::GIFT_MESSAGE];
        }

        return $data[Wrap::USE_GIFT_MESSAGE] ? $data[Wrap::GIFT_MESSAGE] : '';
    }
}
