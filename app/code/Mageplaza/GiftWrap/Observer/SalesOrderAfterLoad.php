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
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as WrapItf;
use Mageplaza\GiftWrap\Helper\Data;

/**
 * Class SalesOrderAfterLoad
 * @package Mageplaza\GiftWrap\Observer
 */
class SalesOrderAfterLoad implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * SalesOrderAfterLoad constructor.
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
        if (!($order instanceof AbstractModel) || !$this->data->isEnabled($order->getStoreId())) {
            return;
        }

        $allCartWrap     = [];
        $allCartPostcard = [];
        $break           = false;
        foreach ($order->getAllItems() as $item) {
            $wrapData     = Data::jsonDecode($item->getData(Data::GIFT_WRAP_DATA));
            $postcardData = Data::jsonDecode($item->getData(Data::GIFT_WRAP_POSTCARD_DATA));
            if (!empty($wrapData[WrapItf::ALL_CART])) {
                $allCartWrap = $wrapData;
                $break       = true;
            }
            if (!empty($postcardData[WrapItf::ALL_CART])) {
                $allCartPostcard = $postcardData;
                $break           = true;
            }

            if ($break) {
                break;
            }
        }

        $this->setEmailParams($order, $allCartWrap, $allCartPostcard);
    }

    /**
     * @param Order $order
     * @param array $allCartWrap
     * @param array $allCartPostcard
     */
    protected function setEmailParams($order, $allCartWrap, $allCartPostcard)
    {
        $wrapName        = empty($allCartWrap['name']) ?: $allCartWrap['name'];
        $postcardName    = empty($allCartPostcard['name']) ?: $allCartPostcard['name'];
        $wrapMessage     = empty($allCartWrap['gift_message']) ?: $allCartWrap['gift_message'];
        $postcardMessage = empty($allCartPostcard['gift_message']) ?: $allCartPostcard['gift_message'];

        $order->setData('mp_gift_wrap_name', $wrapName);
        $order->setData('mp_gift_wrap_message', $wrapMessage);
        $order->setData('mp_gift_wrap_postcard_name', $postcardName);
        $order->setData('mp_gift_wrap_postcard_message', $postcardMessage);
    }
}
