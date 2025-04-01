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

namespace Mageplaza\GiftWrap\Plugin\Controller\Checkout;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Controller\Cart\Add;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as WrapItf;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class Cart
 *
 * @package Mageplaza\GiftWrap\Plugin\Controller\Checkout
 */
class Cart
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Cart constructor.
     *
     * @param Data $data
     * @param CheckoutSession $checkoutSession
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Data $data,
        CheckoutSession $checkoutSession,
        ProductRepositoryInterface $productRepository
    ) {
        $this->data              = $data;
        $this->checkoutSession   = $checkoutSession;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Add $subject
     *
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeExecute(Add $subject)
    {
        $productId = (int) $subject->getRequest()->getParam('product');
        $product   = $this->_initProduct($productId);
        if (!$product || !$this->data->isEnabled()) {
            return;
        }

        /** if current cart has ALL CART gift wrap, apply it for the next none gift wrap item */
        $wrapData = $subject->getRequest()->getParam(Data::GIFT_WRAP_DATA);
        $this->addProductCustomOption(Data::GIFT_WRAP_DATA, $wrapData, $product);

        $postcardData = $subject->getRequest()->getParam(Data::GIFT_WRAP_POSTCARD_DATA);
        $this->addProductCustomOption(Data::GIFT_WRAP_POSTCARD_DATA, $postcardData, $product);
    }

    /**
     * @param $giftDataType
     * @param $data
     * @param $product
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function addProductCustomOption($giftDataType, $data, $product)
    {
        if (empty($data)) {
            $quote = $this->checkoutSession->getQuote();

            /** @var Item $item */
            foreach ($quote->getAllItems() as $item) {
                $itemData = Data::jsonDecode($item->getData($giftDataType));

                if (empty($itemData[WrapItf::ALL_CART])) {
                    continue;
                }

                $data = Data::jsonEncode($itemData);
                break;
            }
        }

        if (empty($data)) {
            return;
        }

        $product->addCustomOption($giftDataType, $data, $product);
    }

    /**
     * Initialize product instance from request data
     *
     * @param int $productId
     *
     * @return false|ProductInterface|Product
     */
    protected function _initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->data->getObject(StoreManagerInterface::class)->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }

        return false;
    }
}
