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

namespace Mageplaza\GiftWrap\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as Wrap;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\Price;

/**
 * Class GiftWrap
 * @package Mageplaza\GiftWrap\Model\Total\Creditmemo
 */
class GiftWrap extends AbstractTotal
{
    /**
     * @type float
     */
    protected $wrapAmount;

    /**
     * @type float
     */
    protected $postcardAmount;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * GiftWrap constructor.
     *
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($data);
    }

    /**
     * @param Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getMpGiftWrapAmount() < 0.0001 && $order->getMpGiftWrapPostcardAmount() < 0.0001) {
            return $this;
        }

        $baseWrapAmount = $wrapAmount = $basePostcardAmount = $postcardAmount = 0;

        if ($order->getMpGiftWrapAmount() > 0.0001) {
            $baseWrapAmount = $this->getGiftWrapAmount($creditmemo, $this->wrapAmount, Data::GIFT_WRAP_DATA);
            $wrapAmount     = $this->helper->convertPrice($baseWrapAmount, false, false, $creditmemo->getStore());

            $appliedBaseAmount = $this->helper->getAppliedBaseAmount($creditmemo);
            $appliedAmount     = $this->helper->getAppliedAmount($creditmemo);

            $baseWrapAmount = min($baseWrapAmount, $order->getMpGiftWrapBaseAmount() - $appliedBaseAmount);
            $wrapAmount     = min($wrapAmount, $order->getMpGiftWrapAmount() - $appliedAmount);

            $creditmemo->setMpGiftWrapBaseAmount($baseWrapAmount);
            $creditmemo->setMpGiftWrapAmount($wrapAmount);
        }

        if ($order->getMpGiftWrapPostcardAmount() > 0.0001) {
            $basePostcardAmount = $this->getGiftWrapAmount(
                $creditmemo,
                $this->postcardAmount,
                Data::GIFT_WRAP_POSTCARD_DATA
            );
            $postcardAmount     = $this->helper->convertPrice(
                $basePostcardAmount,
                false,
                false,
                $creditmemo->getStore()
            );

            $appliedBaseAmount = $this->helper->getAppliedBaseAmount($creditmemo);
            $appliedAmount     = $this->helper->getAppliedAmount($creditmemo);

            $basePostcardAmount = min(
                $basePostcardAmount,
                $order->getMpGiftWrapPostcardBaseAmount() - $appliedBaseAmount
            );
            $postcardAmount     = min($postcardAmount, $order->getMpGiftWrapPostcardAmount() - $appliedAmount);

            $creditmemo->setMpGiftWrapPostcardBaseAmount($basePostcardAmount);
            $creditmemo->setMpGiftWrapPostcardAmount($postcardAmount);
        }

        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseWrapAmount + $basePostcardAmount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $wrapAmount + $postcardAmount);

        return $this;
    }

    /**
     * @param Creditmemo $creditmemo
     * @param float|null $amount
     * @param string $wrapDataType
     *
     * @return float
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getGiftWrapAmount($creditmemo, $amount, $wrapDataType)
    {
        $storeId = $creditmemo->getStoreId();
        $taxRate = $creditmemo->getOrder()->getMpGiftWrapTax();
        if ($amount === null) {
            $amount = $allCartAmount = $allCartMultiplier = $allCartMessageFee = $hasAllCart = 0;

            /** @var Creditmemo\Item $creditmemoItem */
            foreach ($creditmemo->getAllItems() as $creditmemoItem) {
                $item    = $creditmemoItem->getOrderItem();
                $product = $item->getProduct();
                $data    = $this->helper->jsonDecodeData($item->getData($wrapDataType));
                if (empty($data)
                    || $item->getParentItem()
                    || $creditmemoItem->getQty() === 0
                    || ($product && $product->isVirtual())
                    || (!empty($data['all_cart']) && !$this->helper->isShowAllCart($storeId))
                    || (empty($data['all_cart']) && !$this->helper->isShowOnProduct($storeId))
                ) {
                    continue;
                }

                if (!empty($data[Wrap::ALL_CART])) {
                    $allCartAmount     = $data[Wrap::AMOUNT];
                    $allCartMultiplier += (int) $data[Wrap::PRICE_TYPE] === Price::BY_QTY
                        ? $creditmemoItem->getQty() : 0;
                    if ($wrapDataType === Data::GIFT_WRAP_DATA) {
                        $allCartMessageFee = $data[Wrap::USE_GIFT_MESSAGE] ? $data[Wrap::GIFT_MESSAGE_FEE] : 0;
                    }
                    $hasAllCart = (int) $data[Wrap::PRICE_TYPE] === Price::FIXED;

                    continue;
                }

                $multiplier = (int) $data[Wrap::PRICE_TYPE] === Price::BY_QTY ? $creditmemoItem->getQty() : 1;

                $amount += $data[Wrap::AMOUNT] * $multiplier;

                if ($wrapDataType === Data::GIFT_WRAP_DATA && $data[Wrap::USE_GIFT_MESSAGE]) {
                    $amount += $data[Wrap::GIFT_MESSAGE_FEE] * $multiplier;
                }
            }

            $allCartAppliedKey = $wrapDataType === Data::GIFT_WRAP_DATA
                ? 'mp_gift_wrap_all_cart_applied' : 'mp_gift_wrap_postcard_all_cart_applied';
            if (!$creditmemo->getData($allCartAppliedKey)) {
                $amount += ($allCartAmount + $allCartMessageFee) * max($allCartMultiplier, 1);
            }

            $amount *= (1 + $taxRate / 100);

            $creditmemo->setData($allCartAppliedKey, $hasAllCart);
        }

        return $amount;
    }
}
