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

namespace Mageplaza\GiftWrap\Plugin\Model\Quote;

use Closure;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalsExtensionFactory;
use Magento\Quote\Api\Data\TotalsExtensionInterface;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class CartTotalRepository
 * @package Mageplaza\GiftWrap\Plugin\Model\Quote
 */
class CartTotalRepository
{
    /**
     * @var TotalsExtensionFactory
     */
    protected $totalsExtension;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * CartTotalRepository constructor.
     *
     * @param TotalsExtensionFactory $totalsExtension
     * @param CartRepositoryInterface $cartRepository
     * @param Data $helper
     */
    public function __construct(
        TotalsExtensionFactory $totalsExtension,
        CartRepositoryInterface $cartRepository,
        Data $helper
    ) {
        $this->totalsExtension = $totalsExtension;
        $this->cartRepository  = $cartRepository;
        $this->helper          = $helper;
    }

    /**
     * @param CartTotalRepositoryInterface $subject
     * @param \Magento\Quote\Api\Data\TotalsInterface $quoteTotals
     * @param int $cartId
     *
     * @return mixed
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(CartTotalRepositoryInterface $subject, $quoteTotals, $cartId)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        $wrapConfig     = [];
        $postcardConfig = [];

        if ($this->helper->isEnabled($quote->getStoreId())) {
            /** @var Item $item */
            foreach ($quote->getAllItems() as $item) {
                if (!in_array($item->getProduct()->getTypeId(), Data::ALLOWED_TYPE, true)) {
                    continue;
                }

                $wrapConfig[] = [
                    'item_id'            => $item->getId(),
                    Data::GIFT_WRAP_DATA => $item->getData(Data::GIFT_WRAP_DATA),
                ];

                $postcardConfig[] = [
                    'item_id'                     => $item->getId(),
                    Data::GIFT_WRAP_POSTCARD_DATA => $item->getData(Data::GIFT_WRAP_POSTCARD_DATA)
                ];
            }
        }

        /** @var TotalsExtensionInterface $totalsExtension */
        $totalsExtension = $quoteTotals->getExtensionAttributes() ?: $this->totalsExtension->create();
        $totalsExtension->setMpGiftWrap(Data::jsonEncode($wrapConfig));
        $totalsExtension->setMpGiftWrapPostcard(Data::jsonEncode($postcardConfig));

        $quoteTotals->setExtensionAttributes($totalsExtension);

        return $quoteTotals;
    }
}
