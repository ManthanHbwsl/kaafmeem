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
use Magento\Framework\App\RequestInterface;

/**
 * Class ProductItemUpdate
 * @package Mageplaza\GiftWrap\Observer
 */
class ProductItemUpdate implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * AddToCart constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper, RequestInterface $request)
    {
        $this->helper  = $helper;
        $this->request = $request;
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
        $product = $observer->getEvent()->getProduct();

        if ($item) {
            if ($wraps = $this->request->getParam(Data::GIFT_WRAP_DATA)) {
                $this->setDataToItem($item, $product, Data::jsonDecode($wraps), Data::GIFT_WRAP_DATA);
            }

            if ($wraps = $this->request->getParam(Data::GIFT_WRAP_POSTCARD_DATA)) {
                $this->setDataToItem($item, $product, Data::jsonDecode($wraps), Data::GIFT_WRAP_POSTCARD_DATA);
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
    private function setDataToItem($item, $product, $data, $wrapDataType)
    {
        if ($data) {
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
            $product->addCustomOption($wrapDataType, Data::jsonEncode($data), $product);
        } else {
            $item->setData($wrapDataType);
            $product->addCustomOption($wrapDataType, null, $product);
        }
    }
}
