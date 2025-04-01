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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as Wrap;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class ConvertQuoteToOrder
 * @package Mageplaza\GiftWrap\Observer
 */
class ConvertQuoteToOrder implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * ConvertQuoteToOrder constructor.
     *
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        if (!$this->data->isEnabled($order->getStoreId())) {
            return;
        }

        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        if ($quote->getMpGiftWrapAmount()) {
            $order->setMpGiftWrapBaseAmount($quote->getMpGiftWrapBaseAmount());
            $order->setMpGiftWrapAmount($quote->getMpGiftWrapAmount());
        }

        if ($quote->getMpGiftWrapPostcardAmount()) {
            $order->setMpGiftWrapPostcardBaseAmount($quote->getMpGiftWrapPostcardBaseAmount());
            $order->setMpGiftWrapPostcardAmount($quote->getMpGiftWrapPostcardAmount());
        }

        $order->setMpGiftWrapTax($quote->getMpGiftWrapTax());
        $allCartWrap     = $this->addDataToOrderItem($quote, $order, Data::GIFT_WRAP_DATA);
        $allCartPostcard = $this->addDataToOrderItem($quote, $order, Data::GIFT_WRAP_POSTCARD_DATA);

        $this->setEmailParams($order, $allCartWrap, $allCartPostcard);
    }

    /**
     * @param Quote $quote
     * @param Order $order
     * @param string $wrapDataType
     *
     * @return array|mixed
     */
    private function addDataToOrderItem($quote, $order, $wrapDataType)
    {
        $storeId     = $quote->getStoreId();
        $allCartWrap = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $orderItem = $order->getItemByQuoteItemId($item->getId());

            if (!$orderItem) {
                continue;
            }

            $data = Data::jsonDecode($item->getData($wrapDataType));

            if (empty($data)
                || $item->getParentItem()
                || $item->getQty() === 0
                || $item->getProduct()->isVirtual()
                || (!empty($data['all_cart']) && !$this->data->isShowAllCart($storeId))
                || (empty($data['all_cart']) && !$this->data->isShowOnProduct($storeId))
            ) {
                continue;
            }

            if (!empty($data[Wrap::USE_GIFT_MESSAGE])) {
                $data[Wrap::GIFT_MESSAGE_FEE] = $this->data->getGiftMessageFee(false, false, $storeId);
            }

            if (!empty($data[Wrap::ALL_CART])) {
                $allCartWrap = $data;
            }

            $orderItem->setData($wrapDataType, Data::jsonEncode($data));
        }

        return $allCartWrap;
    }

    /**
     * @param Order $order
     * @param array $allCartWrap
     * @param array $allCartPostcard
     */
    protected function setEmailParams($order, $allCartWrap, $allCartPostcard)
    {
        $wrapName        = empty($allCartWrap[Wrap::NAME]) ?: $allCartWrap[Wrap::NAME];
        $postcardName    = empty($allCartPostcard[Wrap::NAME]) ?: $allCartPostcard[Wrap::NAME];
        $wrapMessage     = empty($allCartWrap[Wrap::GIFT_MESSAGE]) ?: $allCartWrap[Wrap::GIFT_MESSAGE];
        $postcardMessage = empty($allCartPostcard[Wrap::GIFT_MESSAGE]) ?: $allCartPostcard[Wrap::GIFT_MESSAGE];

        $order->setData('mp_gift_wrap_name', $wrapName);
        $order->setData('mp_gift_wrap_message', $wrapMessage);
        $order->setData('mp_gift_wrap_postcard_name', $postcardName);
        $order->setData('mp_gift_wrap_postcard_message', $postcardMessage);
    }
}
