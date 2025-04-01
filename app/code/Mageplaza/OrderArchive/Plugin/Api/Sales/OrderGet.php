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
 * @category  Mageplaza
 * @package   Mageplaza_OrderArchive
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderArchive\Plugin\Api\Sales;

use Magento\Sales\Api\Data\OrderExtensionInterfaceFactory as OrderEXFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Mageplaza\OrderArchive\Helper\Data as HelperData;

/**
 * Class OrderGet
 *
 * @package Mageplaza\MultipleCoupons\Plugin\Api\Sales
 */
class OrderGet
{
    /**
     * @var OrderEXFactory
     */
    protected $orderExFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * OrderGet constructor.
     *
     * @param OrderEXFactory $orderExFactory
     * @param HelperData $helperData
     */
    public function __construct(
        OrderEXFactory $orderExFactory,
        HelperData $helperData
    ) {
        $this->orderExFactory = $orderExFactory;
        $this->_helperData    = $helperData;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     *
     * @return OrderInterface
     * @SuppressWarnings(Unused)
     */
    public function afterGet(OrderRepositoryInterface $subject, $resultOrder)
    {
        if ($this->_helperData->isEnabled()) {
            $isArchive  = $resultOrder->getIsArchive() ?: 0;
            $attributes = $resultOrder->getExtensionAttributes();

            if ($attributes === null) {
                $attributes = $this->orderExFactory->create();
            }

            $attributes->setIsArchive($isArchive);
            $resultOrder->setExtensionAttributes($attributes);
        }

        return $resultOrder;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $result
     *
     * @return mixed
     * @SuppressWarnings(Unused)
     */
    public function afterGetList(OrderRepositoryInterface $subject, $result)
    {
        if ($this->_helperData->isEnabled()) {
            foreach ($result->getItems() as $item) {
                $isArchive  = $item->getIsArchive() ?: 0;
                $attributes = $item->getExtensionAttributes();

                if ($attributes === null) {
                    $attributes = $this->orderExFactory->create();
                }

                $attributes->setIsArchive($isArchive);
                $item->setExtensionAttributes($attributes);
            }
        }

        return $result;
    }
}
