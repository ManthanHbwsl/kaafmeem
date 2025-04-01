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
 * @package     Mageplaza_FreeShippingBar
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FreeShippingBar\Block;

use Exception;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Widget\Block\BlockInterface;
use Mageplaza\FreeShippingBar\Helper\Data;
use Mageplaza\FreeShippingBar\Model\Api\ShippingBarRepository;
use Mageplaza\FreeShippingBar\Model\Shippingbar;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Position;

/**
 * Class FreeShippingBar
 * @package Mageplaza\FreeShippingBar\Block
 */
class FreeShippingBar extends AbstractProduct implements BlockInterface
{
    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var ShippingBarRepository
     */
    protected $shippingBarRepository;

    /**
     * FreeShippingBar constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param ShippingBarRepository $shippingBarRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helperData,
        ShippingBarRepository $shippingBarRepository,
        array $data = []
    ) {
        $this->_helperData = $helperData;
        $this->shippingBarRepository = $shippingBarRepository;

        parent::__construct($context, $data);
    }

    /**
     * @return Shippingbar
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getShippingBarSnippet()
    {
        $customerGroupId = $this->getCustomerGroupId();
        $shippingBarCollection = $this->shippingBarRepository->getShippingBars($customerGroupId);
        $ruleId = $this->getRuleId();
        $shippingBarCollection->addFieldToFilter('position', Position::INSERT_SNIPPET)
            ->addFieldToFilter('rule_id', $ruleId);

        /** @var Shippingbar $shippingBar */
        $shippingBar = $shippingBarCollection->getFirstItem();

        return $shippingBar;
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->_helperData->getCustomerGroupId();
    }

    /**
     * @param mixed $amount
     *
     * @return float
     */
    public function getGoalByStore($amount)
    {
        try {
            /** @var Store $store */
            $store = $this->_storeManager->getStore();

            return $store->getBaseCurrency()->convert((float)$amount, $store->getCurrentCurrency()->getCode());
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());

            return $amount;
        }
    }

    /**
     * @param string $imageShippingBar
     * @param string $templateIdShippingBar
     *
     * @return string|null
     */
    public function getImageBackgroundUrl($imageShippingBar, $templateIdShippingBar)
    {
        return $this->_helperData->getImageBackgroundUrl($imageShippingBar, $templateIdShippingBar);
    }

    /**
     * @return string
     */
    public function getLocalPriceFormat()
    {
        $data = new DataObject([
            'priceFormat' => $this->_helperData->getPriceFormat()
        ]);

        return Data::jsonEncode($data);
    }

    /**
     * @return Shippingbar[]
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getShippingBarCollection()
    {
        $customerGroupId = $this->getCustomerGroupId();
        $fullActionName = $this->getRequest()->getFullActionName();
        $collection = $this->shippingBarRepository->getShippingBars($customerGroupId);
        $currentPath = $this->getRequest()->getRequestUri();
        $layoutPosition = $this->getPosition();

        switch ($layoutPosition) {
            case 'top_content':
                $position = Position::TOP_CONTENT;
                break;
            case 'top_bottom_page_position':
                $position = [
                    'in' => [
                        Position::BOTTOM_FIXED_BAR,
                        Position::TOP_FIXED_BAR,
                        Position::TOP_PAGE
                    ]
                ];
                break;
            default:
                $position = '';
        }

        $result = $this->shippingBarRepository->getShippingBarByPosition(
            $collection,
            $position,
            $fullActionName,
            $currentPath
        );

        return array_filter($result);
    }
}
