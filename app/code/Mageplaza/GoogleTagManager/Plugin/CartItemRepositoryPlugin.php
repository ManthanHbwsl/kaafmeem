<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GoogleTagManager
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GoogleTagManager\Plugin;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote;
use Mageplaza\GoogleTagManager\Api\Data\GtmCartItemInterface;
use Mageplaza\GoogleTagManager\Helper\Data;

/**
 * Class CartItemRepositoryPlugin
 * @package Mageplaza\GoogleTagManager\Plugin
 */
class CartItemRepositoryPlugin
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var GtmCartItemInterface
     */
    protected $gtmCartItem;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var array
     */
    private $removeItem = [];

    /**
     * CartItemRepositoryPlugin constructor.
     *
     * @param Data $helperData
     * @param ProductRepositoryInterface $productRepository
     * @param GtmCartItemInterface $gtmCartItem
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        Data $helperData,
        ProductRepositoryInterface $productRepository,
        GtmCartItemInterface $gtmCartItem,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->helperData        = $helperData;
        $this->productRepository = $productRepository;
        $this->gtmCartItem       = $gtmCartItem;
        $this->quoteRepository   = $quoteRepository;
    }

    /**
     * @param CartItemRepositoryInterface $subject
     * @param CartItemInterface $result
     * @param CartItemInterface $cartItem
     *
     * @return CartItemInterface
     */
    public function afterSave(CartItemRepositoryInterface $subject, $result, $cartItem)
    {
        if ($this->helperData->isEnabled() && $cartItem->getExtensionAttributes()) {
            try {
                /** @var Product $product */
                $product = $this->productRepository->get($cartItem->getSku());
                $htmlGtm = '';
                $htmlGa  = '';
                $htmlFb  = '';

                if ($this->helperData->getConfigGTM('enabled')) {
                    $productsGa4 = [];
                    $productsGTM = $this->helperData->getGTMAddToCartData($product, $cartItem->getQty());

                    if ($this->helperData->isEnabledGTMGa4()) {
                        $productsGa4 = $this->helperData->getGTMGa4AddToCartData($product, $cartItem->getQty());
                    }

                    $dataGTM = [
                        'event'     => 'addToCart',
                        'ecommerce' => [
                            'currencyCode' => $this->helperData->getCurrentCurrency(),
                            'add'          => [
                                'products' => [$productsGTM]
                            ]
                        ]
                    ];

                    if ($this->helperData->isEnabledGTMGa4()) {
                        $dataGTM['ga4_event']          = 'add_to_cart';
                        $dataGTM['ecommerce']['items'] = [$productsGa4];
                    }
                    $htmlGtm = '<script>dataLayer.push(' . Data::jsonEncode($dataGTM) . ')</script>';
                }

                if ($this->helperData->getConfigAnalytics('enabled')) {
                    $productsGA = $this->helperData->getGAAddToCartData($product, $cartItem->getQty());
                    $dataGA     = [
                        'items' => [$productsGA],
                    ];

                    $htmlGa = '<script>gtag("event", "add_to_cart", ' . Data::jsonEncode($dataGA) . ')</script>';
                }

                if ($this->helperData->getConfigPixel('enabled')) {
                    $productsFB = $this->helperData->getFBAddToCartData($product, $cartItem->getQty());
                    $dataFB     = [
                        'content_ids'  => [$productsFB['id']],
                        'content_name' => [$productsFB['name']],
                        'content_type' => 'product',
                        'contents'     => [$productsFB],
                        'currency'     => $this->helperData->getCurrentCurrency(),
                        'value'        => (float) $productsFB['price']
                    ];

                    $htmlFb = '<script>fbq("track", "AddToCart", ' . Data::jsonEncode($dataFB) . ')</script>';
                }

                $this->gtmCartItem->setGtmAddToCart($htmlGtm);
                $this->gtmCartItem->setGaAddToCart($htmlGa);
                $this->gtmCartItem->setFbAddToCart($htmlFb);
                $html[] = $this->gtmCartItem;
            } catch (Exception $e) {
                $html = [];
            }

            if ($result->getExtensionAttributes() && !empty($html)) {
                $result->getExtensionAttributes()->setMpGtmAddToCart($html);
            }
        }

        return $result;
    }

    /**
     * @param CartItemRepositoryInterface $subject
     * @param int $cartId
     * @param int $itemId
     *
     * @return null
     */
    public function beforeDeleteById(CartItemRepositoryInterface $subject, $cartId, $itemId)
    {
        try {
            /** @var Quote $quote */
            $quote = $this->quoteRepository->getActive($cartId);
            $quoteItem = $quote->getItemById($itemId);
            $this->removeItem = [];
            if ($this->helperData->isEnabled() && $quoteItem && $quoteItem->getId()) {
                /** @var Product $product */
                $product = $this->productRepository->get($quoteItem->getSku());

                if ($this->helperData->getConfigGTM('enabled')) {
                    $dataGTM = $this->helperData->getGTMRemoveFromCartData($product, $quoteItem->getQty());
                    $this->removeItem[] = '<script>dataLayer.push(' . Data::jsonEncode($dataGTM) . ')</script>';
                }

                if ($this->helperData->getConfigAnalytics('enabled')) {
                    $dataGA = $this->helperData->getGARemoveFromCartData($product, $quoteItem->getQty());
                    $this->removeItem[] = '<script>gtag("event", "remove_from_cart", ' . Data::jsonEncode($dataGA) . ');</script>';
                }
            }
        } catch (Exception $e) {
            $this->removeItem = [];
        }

        return null;
    }

    public function afterDeleteById(CartItemRepositoryInterface $subject, $result)
    {
        if ($result) {
            return $this->removeItem;
        }

        return $result;
    }
}
