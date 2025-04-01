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

use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class AddToCart
 * @package Mageplaza\GiftWrap\Observer
 */
class AddToCart implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * AddToCart constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Item|string $item */
        $item = $observer->getEvent()->getQuoteItem();
        /** @var Product $product */
        $product      = $observer->getEvent()->getProduct();
        $wrapData     = $product->getCustomOption(Data::GIFT_WRAP_DATA);
        $postcardData = $product->getCustomOption(Data::GIFT_WRAP_POSTCARD_DATA);

        if ($item) {
            if (!empty($wrapData['value'])) {
                $wraps = Data::jsonDecode($wrapData['value']);
                $this->setDataToItem($item, $wraps, Data::GIFT_WRAP_DATA);
            }
            if (!empty($postcardData['value'])) {
                $postcards = Data::jsonDecode($postcardData['value']);
                $this->setDataToItem($item, $postcards, Data::GIFT_WRAP_POSTCARD_DATA);
            }
        }
    }

    /**
     * @param Item|string $item
     * @param array $data
     * @param string $wrapDataType
     *
     * @throws LocalizedException
     */
    private function setDataToItem($item, $data, $wrapDataType)
    {
        $this->helper->validateWrap($data);

        if (!empty($data['gift_message'])) {
            $data['gift_message'] = mb_substr(
                $data['gift_message'],
                0,
                $this->helper->getMaxCharacters(),
                'utf-8'
            );
        }

        $item->setData($wrapDataType, Data::jsonEncode($data));
    }
}
