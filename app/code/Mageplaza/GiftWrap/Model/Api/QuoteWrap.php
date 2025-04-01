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

namespace Mageplaza\GiftWrap\Model\Api;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\CartItemInterfaceFactory;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as WrapItf;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterfaceFactory;
use Mageplaza\GiftWrap\Api\QuoteWrapInterface;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\Type;
use Mageplaza\GiftWrap\Model\Wrap;
use Mageplaza\GiftWrap\Model\WrapFactory;
use Mageplaza\GiftWrap\Model\Config\Source\WrapType;

/**
 * Class QuoteWrap
 * @package Mageplaza\GiftWrap\Model\Api
 */
class QuoteWrap implements QuoteWrapInterface
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var CartTotalRepositoryInterface
     */
    private $cartTotalRepository;

    /**
     * @var WrapFactory
     */
    private $wrapFactory;

    /**
     * @var CartItemInterfaceFactory
     */
    private $wrapItemFactory;

    /**
     * @var WrapDetailsInterfaceFactory
     */
    private $wrapDetailsFactory;

    /**
     * QuoteWrap constructor.
     *
     * @param Data $helper
     * @param CartRepositoryInterface $cartRepository
     * @param CartTotalRepositoryInterface $cartTotalRepository
     * @param WrapFactory $wrapFactory
     * @param CartItemInterfaceFactory $wrapItemFactory
     * @param WrapDetailsInterfaceFactory $wrapDetailsFactory
     */
    public function __construct(
        Data $helper,
        CartRepositoryInterface $cartRepository,
        CartTotalRepositoryInterface $cartTotalRepository,
        WrapFactory $wrapFactory,
        CartItemInterfaceFactory $wrapItemFactory,
        WrapDetailsInterfaceFactory $wrapDetailsFactory
    ) {
        $this->helper              = $helper;
        $this->cartRepository      = $cartRepository;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->wrapFactory         = $wrapFactory;
        $this->wrapItemFactory     = $wrapItemFactory;
        $this->wrapDetailsFactory  = $wrapDetailsFactory;
    }

    /**
     * {@inheritDoc}
     * @param Wrap|array $wrap
     * @param Wrap|array $postcard
     */
    public function updateGiftWrap($cartId, $itemId, $wrap, $postcard)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        $this->addWrapOptionToItem($quote, $itemId, $wrap, Data::GIFT_WRAP_DATA);
        $this->addWrapOptionToItem($quote, $itemId, $postcard, Data::GIFT_WRAP_POSTCARD_DATA);

        $this->cartRepository->save($quote->collectTotals());

        return $this->cartTotalRepository->get($quote->getId());
    }

    /**
     * @param Quote $quote
     * @param int $itemId
     * @param Wrap $wrapObj
     * @param string $wrapDataType
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function addWrapOptionToItem($quote, $itemId, $wrapObj, $wrapDataType)
    {
        $storeId = $quote->getStoreId();
        $wrap    = $wrapObj->getData();
        if (!empty($wrap[WrapItf::GIFT_MESSAGE])) {
            $maxChars = $this->helper->getMaxCharacters($storeId);

            $wrap[WrapItf::GIFT_MESSAGE] = mb_substr($wrap[WrapItf::GIFT_MESSAGE], 0, $maxChars, 'utf-8');
        }

        if ($itemId === WrapItf::ALL_CART) {
            $wrapObj->setAllCart(true);
            foreach ($quote->getAllVisibleItems() as $item) {
                $data    = $this->helper->jsonDecodeData($item->getData($wrapDataType));
                $product = $item->getProduct();

                if ((!empty($data) && empty($data[WrapItf::ALL_CART]) && $this->helper->isShowOnProduct($storeId))
                    || $item->getParentItem()
                    || !in_array($product->getTypeId(), Data::ALLOWED_TYPE, true)
                ) {
                    continue;
                }

                $this->addWrapOption($item, $wrapObj, $wrapDataType);
            }
        } else {
            if (!$item = $quote->getItemById($itemId)) {
                throw NoSuchEntityException::singleField('itemId', $itemId);
            }
            $this->addWrapOption($item, $wrapObj, $wrapDataType);
        }
    }

    /**
     * @param Item $item
     * @param Wrap|mixed $wrapObj
     * @param string $wrapDataType
     *
     * @throws LocalizedException
     */
    private function addWrapOption($item, $wrapObj, $wrapDataType)
    {
        $wrap = $wrapObj->getData();
        if ($wrap && $wrapObj->getCategory()) {
            $this->helper->validateWrap($wrap);
        }

        if ($wrapDataType === Data::GIFT_WRAP_DATA && $wrap && $wrapObj->getId() && $wrapObj->getQtyLimit()) {
            $this->limitQty($item->getQuote(), $item, $wrap);
        }

        $wrapData = $wrap ? $this->helper->jsonEncodeData($wrap) : null;

        $product = $item->getProduct();

        /** @var DataObject|array $option */
        $option = [
            'item_id'    => $item->getId(),
            'product_id' => $product->getId(),
            'code'       => $wrapDataType,
            'value'      => $wrapData,
            'product'    => $product
        ];

        $item->addOption($option);
        $item->setData($wrapDataType, $wrapData);
        $product->addCustomOption($wrapDataType, $wrapData, $product);
    }

    /**
     * @param Quote $quote
     * @param Item $currentItem
     * @param Wrap $wrapObj
     *
     * @throws LocalizedException
     */
    private function limitQty($quote, $currentItem, $wrapObj)
    {
        if ($wrapQtyLimit = $wrapObj->getQtyLimit()) {
            $currentQty = $currentItem->getQty();
            if ($currentQty > $wrapQtyLimit) {
                throw new LocalizedException(__('We don\'t have enough wrappers to use for this item qty.'));
            }
            /** @var Item $item */
            foreach ($quote->getAllItems() as $item) {
                $wrapItemData = Data::jsonDecode($item->getData(Data::GIFT_WRAP_DATA));
                if (empty($wrapItemData)
                    || $wrapObj->getId() !== $wrapItemData[WrapDetailsInterface::WRAP_ID]
                    || $currentItem->getId() === $item->getId()) {
                    continue;
                }

                $currentQty += $item->getQty();
                if ($currentQty > $wrapQtyLimit) {
                    throw new LocalizedException(__('We don\'t have enough wrappers to use for this item qty.'));
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function set($cartId, $itemId, $wrapId, $message = '')
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        if ($this->helper->getGiftWrapType($quote->getStoreId()) === Type::ALL) {
            throw new LocalizedException(__('Cannot apply gift wrap for the item due to the configuration in admin panel.'));
        }

        $wrapObj = $this->wrapFactory->create()->load($wrapId);

        if (!$wrapObj->getId()) {
            throw NoSuchEntityException::singleField('wrapId', $wrapId);
        }

        $wrap = $wrapObj->getData();

        $this->helper->processWrap($wrap);

        $wrap[WrapItf::GIFT_MESSAGE]     = $message;
        $wrap[WrapItf::USE_GIFT_MESSAGE] = $message !== '';
        if ($wrap[WrapItf::USE_GIFT_MESSAGE]) {
            $wrap[WrapItf::GIFT_MESSAGE_FEE] = $this->helper->getGiftMessageFee(false, false, $quote->getStoreId());
        }

        $wrapDataType = $wrapObj->getWrapType() === WrapType::GIFT_WRAP_WRAP_TYPE
            ? Data::GIFT_WRAP_DATA : Data::GIFT_WRAP_POSTCARD_DATA;
        $this->addWrapOptionToItem($quote, $itemId, $wrapObj, $wrapDataType);
        $this->cartRepository->save($quote->collectTotals());

        $item = $this->cartRepository->getActive($cartId)->getItemById($itemId);

        return $this->getWrapItem($item);
    }

    /**
     * @inheritDoc
     */
    public function remove($cartId, $itemId)
    {
        $this->updateGiftWrap($cartId, $itemId, [], []);

        $item = $this->cartRepository->getActive($cartId)->getItemById($itemId);

        return $this->getWrapItem($item);
    }

    /**
     * @param Item|false $item
     *
     * @return Item
     */
    private function getWrapItem($item)
    {
        /** @var Item $wrapItem */
        $wrapItem = $this->wrapItemFactory->create();

        if (!$item) {
            return $wrapItem;
        }

        $wrapItem->setData($item->getData());

        if ($wrap = $item->getData(Data::GIFT_WRAP_DATA)) {
            /** @var Wrap $wrapDetails */
            $wrapDetails = $this->wrapDetailsFactory->create();
            $wrapDetails->setData(Data::jsonDecode($wrap));
            $wrapItem->setData(Data::GIFT_WRAP_DATA, $wrapDetails);
        }

        return $wrapItem;
    }

    /**
     * @inheritDoc
     */
    public function setAll($cartId, $wrapId, $message = '')
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        if ($this->helper->getGiftWrapType($quote->getStoreId()) === Type::ITEM) {
            throw new LocalizedException(__('Cannot apply gift wrap for the whole cart due to the configuration in admin panel.'));
        }

        /** @var TYPE_NAME $message */
        $this->set($cartId, WrapItf::ALL_CART, $wrapId, $message);

        return $this->getWrapItemAll($quote);
    }

    /**
     * @inheritDoc
     */
    public function removeAll($cartId)
    {
        $this->remove($cartId, WrapItf::ALL_CART);

        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        return $this->getWrapItemAll($quote);
    }

    /**
     * @param Quote $quote
     *
     * @return array
     */
    private function getWrapItemAll($quote)
    {
        $wrapItems = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $wrapItems[] = $this->getWrapItem($item);
        }

        return $wrapItems;
    }
}
