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

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Model\Cart;

/**
 * Class PaypalPrepareItems
 * @package Mageplaza\GiftWrap\Observer
 */
class PaypalPrepareItems implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * PaypalPrepareItems constructor.
     *
     * @param Session $checkoutSession
     */
    public function __construct(Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Add gift wrap amount to payment total
     *
     * @param Observer $observer
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart     = $observer->getEvent()->getCart();
        $giftWrap = $this->checkoutSession->getQuote()->getData('mp_gift_wrap_base_amount');
        if ($giftWrap > 0.0001) {
            $cart->addCustomItem(__('Gift Wrap'), 1, $giftWrap);
        }

        $postcard = $this->checkoutSession->getQuote()->getData('mp_gift_wrap_postcard_base_amount');
        if ($postcard > 0.0001) {
            $cart->addCustomItem(__('Postcard'), 1, $postcard);
        }
    }
}
