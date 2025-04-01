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

namespace Mageplaza\GiftWrap\Plugin\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\ResourceModel\Wrap as WrapResource;
use Mageplaza\GiftWrap\Model\Wrap;
use Mageplaza\GiftWrap\Model\WrapFactory;

/**
 * Class Quote
 * @package Mageplaza\GiftWrap\Plugin\Model
 */
class Quote
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var WrapResource
     */
    private $wrapResource;

    /**
     * @var WrapFactory
     */
    private $wrap;

    /**
     * Quote constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param WrapFactory $wrap
     * @param WrapResource $wrapResource
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        WrapFactory $wrap,
        WrapResource $wrapResource
    ) {
        $this->productRepository = $productRepository;
        $this->wrapResource      = $wrapResource;
        $this->wrap              = $wrap;
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @param int $itemId
     * @param DataObject $buyRequest
     * @param null|array|DataObject $params
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforeUpdateItem(\Magento\Quote\Model\Quote $subject, $itemId, $buyRequest, $params = null)
    {
        $item    = $subject->getItemById($itemId);
        $prodId  = $item->getProduct()->getId();
        $product = $this->productRepository->getById($prodId, false, $subject->getStore()->getId());

        if ($option = $item->getOptionByCode(Data::GIFT_WRAP_DATA)) {
            $product->addCustomOption(Data::GIFT_WRAP_DATA, $option->getValue(), $product);
        }

        return [$itemId, $buyRequest, $params];
    }

    /**
     * @param \Magento\Quote\Model\Quote $subject
     * @param Product $product
     * @param null $request
     * @param string $processMode
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeAddProduct(
        \Magento\Quote\Model\Quote $subject,
        Product $product,
        $request = null,
        $processMode = AbstractType::PROCESS_MODE_FULL
    ) {
        if (!$request instanceof \Magento\Framework\DataObject) {
            return [$product, $request, $processMode];
        }
        $mpGiftWrapData = Data::jsonDecode($request->getData(Data::GIFT_WRAP_DATA));
        if (($itemId = $request->getData('id')) && empty($mpGiftWrapData)) {
            $mpGiftWrapData = Data::jsonDecode($subject->getItemById($itemId)->getData(Data::GIFT_WRAP_DATA));
        }

        if (!empty($mpGiftWrapData)) {
            /** @var Wrap $wrap */
            $wrap = $this->wrap->create();
            $this->wrapResource->load($wrap, $mpGiftWrapData['wrap_id']);

            if ($qtyLimit = $wrap->getQtyLimit()) {
                if ($request->getData('qty') > $qtyLimit) {
                    throw new LocalizedException(__('We don\'t have enough wrappers to use for this item qty.'));
                }

                $wrapItemQty = 0;
                /** @var Item $item */
                foreach ($subject->getAllItems() as $item) {
                    $wrapItemData = Data::jsonDecode($item->getData(Data::GIFT_WRAP_DATA));
                    if (isset($wrapItemData[WrapDetailsInterface::WRAP_ID])
                        && $wrapItemData[WrapDetailsInterface::WRAP_ID] === $wrap->getId()) {
                        if ($itemId === $item->getId()) {
                            $wrapItemQty += $request->getData('qty');
                            $totalQty    = $wrapItemQty;
                        } else {
                            $wrapItemQty += $item->getQty();
                            $totalQty    = $wrapItemQty + $request->getData('qty');
                        }
                        if ($totalQty > $qtyLimit) {
                            throw new LocalizedException(__('We don\'t have enough wrappers to use for this item qty.'));
                        }
                    }
                }
            }
        }

        return [$product, $request, $processMode];
    }
}
