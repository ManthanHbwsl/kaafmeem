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

namespace Mageplaza\GiftWrap\Model\Total\Quote;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Tax\Model\Calculation;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as Wrap;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\Price;

/**
 * Class GiftWrap
 * @package Mageplaza\GiftWrap\Model\Total\Quote
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
     * @var Calculation
     */
    protected $calculationTool;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $data;

    /**
     * @var Data
     */
    protected $taxRate;

    /**
     * GiftWrap constructor.
     *
     * @param Calculation $calculationTool
     * @param Data $data
     * @param RequestInterface $request
     */
    public function __construct(
        Calculation $calculationTool,
        Data $data,
        RequestInterface $request
    ) {
        $this->calculationTool = $calculationTool;
        $this->data            = $data;
        $this->request         = $request;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     *
     * @return $this
     * @throws LocalizedException
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        if (!$this->data->isEnabled($quote->getStoreId())
            || ($quote->isVirtual() && $this->_getAddress()->getAddressType() === Quote\Address::ADDRESS_TYPE_SHIPPING)
            || (!$quote->isVirtual() && $this->_getAddress()->getAddressType() === Quote\Address::ADDRESS_TYPE_BILLING)
        ) {
            return $this;
        }

        $baseWrapAmount     = $this->getGiftWrapAmount($quote, $this->wrapAmount, Data::GIFT_WRAP_DATA);
        $basePostcardAmount = $this->getGiftWrapAmount($quote, $this->wrapAmount, Data::GIFT_WRAP_POSTCARD_DATA);
        $wrapAmount         = $this->data->convertPrice($baseWrapAmount, false, false, $quote->getStore());
        $postcardAmount     = $this->data->convertPrice($basePostcardAmount, false, false, $quote->getStore());

        $this->_addBaseAmount($baseWrapAmount + $basePostcardAmount);
        $this->_addAmount($wrapAmount + $postcardAmount);

        $quote->setMpGiftWrapBaseAmount($baseWrapAmount);
        $quote->setMpGiftWrapAmount($wrapAmount);
        $quote->setMpGiftWrapPostcardBaseAmount($basePostcardAmount);
        $quote->setMpGiftWrapPostcardAmount($postcardAmount);
        $quote->setMpGiftWrapTax($this->taxRate);

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Address\Total $total
     *
     * @return array
     * @SuppressWarnings(Unused)
     */
    public function fetch(Quote $quote, Total $total)
    {
        if (!$this->data->isEnabled($quote->getStoreId())) {
            return [];
        }

        $totals = [];

        $wrapAmount = $this->getGiftWrapAmount($quote, $this->wrapAmount, Data::GIFT_WRAP_DATA);
        if ($wrapAmount > 0.0001) {
            $totals[] = [
                'code'  => $this->getCode(),
                'value' => $this->data->convertPrice($wrapAmount, false, false, $quote->getStoreId()),
                'title' => $this->taxRate ? __('Gift Wrap (%1% tax)', $this->taxRate) : __('Gift Wrap')
            ];
        }

        $postcardAmount = $this->getGiftWrapAmount($quote, $this->postcardAmount, Data::GIFT_WRAP_POSTCARD_DATA);
        if ($postcardAmount > 0.0001) {
            $totals[] = [
                'code'  => 'mp_gift_wrap_postcard_amount',
                'value' => $this->data->convertPrice($postcardAmount, false, false, $quote->getStoreId()),
                'title' => $this->taxRate ? __('Postcard (%1% tax)', $this->taxRate) : __('Postcard')
            ];
        }

        return $totals;
    }

    /**
     * @param Quote $quote
     * @param float|null $amount
     * @param string $wrapDataType
     *
     * @return float|int|int[]|mixed
     * @throws LocalizedException
     */
    public function getGiftWrapAmount($quote, $amount, $wrapDataType)
    {
        if (in_array(
            $this->request->getFullActionName(),
            ['multishipping_checkout_overviewPost', 'multishipping_checkout_overview'],
            true
        )) {
            $this->taxRate = 0;

            return 0;
        }

        $storeId              = $quote->getStoreId();
        $addressRequestObject = $this->calculationTool->getRateRequest(
            $quote->getShippingAddress(),
            $quote->getBillingAddress(),
            null,
            $storeId,
            $quote->getCustomerId()
        );
        $addressRequestObject->setProductClassId($this->data->getTaxClass($storeId));

        $this->taxRate = $this->calculationTool->getRate($addressRequestObject);

        if ($amount === null) {
            $amount         = $allCartAmount = $allCartMultiplier = $allCartMessageFee = 0;
            $giftMessageFee = $this->data->getGiftMessageFee(false, false, $storeId);

            foreach ($quote->getAllVisibleItems() as $item) {
                $data = $this->data->jsonDecodeData($item->getData($wrapDataType));

                if (empty($data)
                    || $item->getParentItem()
                    || $item->getQty() === 0
                    || $item->getProduct()->isVirtual()
                    || (!empty($data['all_cart']) && !$this->data->isShowAllCart($storeId))
                    || (empty($data['all_cart']) && !$this->data->isShowOnProduct($storeId))
                ) {
                    continue;
                }

                if (!empty($data['price'])) {
                    $data['price'] = $this->data->convertPrice($data['amount'], true, false, $storeId);
                    $item->setData($wrapDataType, $this->data->jsonEncodeData($data));
                }

                if ($this->isValid($item, $data, $wrapDataType)) {
                    if (!empty($data[Wrap::ALL_CART])) {
                        $allCartAmount     = $data[Wrap::AMOUNT];
                        $allCartMultiplier += (int) $data[Wrap::PRICE_TYPE] === Price::BY_QTY ? $item->getQty() : 0;
                        if ($wrapDataType === Data::GIFT_WRAP_DATA) {
                            $allCartMessageFee = isset($data[Wrap::USE_GIFT_MESSAGE]) ? $giftMessageFee : 0;
                        }

                        continue;
                    }

                    $multiplier = (int) $data[Wrap::PRICE_TYPE] === Price::BY_QTY ? $item->getQty() : 1;

                    $amount += $data[Wrap::AMOUNT] * $multiplier;

                    if ($wrapDataType === Data::GIFT_WRAP_DATA && isset($data[Wrap::USE_GIFT_MESSAGE])) {
                        $amount += $giftMessageFee * $multiplier;
                    }
                }
            }

            $amount += ($allCartAmount + $allCartMessageFee) * max($allCartMultiplier, 1);
            $amount *= (1 + $this->taxRate / 100);
        }

        return $amount;
    }

    /**
     * Remove gift wrap on quote item if the gift wrap invalid (removed, disabled, changed customer group, websites)
     *
     * @param Quote\Item $item
     * @param array|mixed $giftWrap
     * @param string $wrapDataType
     *
     * @return bool
     * @throws LocalizedException
     */
    public function isValid(
        $item,
        $giftWrap,
        $wrapDataType
    ) {
        $result = $this->data->validateWrap($giftWrap, false);
        if (!$result) {
            $item->setData($wrapDataType);
        }

        return $result;
    }
}
