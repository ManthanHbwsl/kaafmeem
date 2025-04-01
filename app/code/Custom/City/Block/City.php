<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template;
use Custom\City\System\Config\Config as CityConfig;

class City extends Template
{
    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var CityConfig  */
    protected $cityConfig;

    private $storeId;

    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        CityConfig $cityConfig
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->storeId = $this->storeManager->getStore()->getId();
        $this->cityConfig = $cityConfig;
    }

    public function isEnabled()
    {
        return $this->cityConfig->isEnabled($this->storeId);
    }

    public function isShippingRateEnabled()
    {
        return $this->cityConfig->isShippingRateEnabled($this->storeId);
    }

    public function isStateAvailable()
    {
        return $this->cityConfig->isStateAvailable($this->storeId);
    }

    public function cityNotInList()
    {
        return $this->cityConfig->cityNotInList($this->storeId);
    }

    public function cityNotInListTitle()
    {
        return $this->cityConfig->cityNotInListTitle($this->storeId);
    }

    public function zipNotInList()
    {
        return $this->cityConfig->zipNotInList($this->storeId);
    }

    public function zipNotInListTitle()
    {
        return $this->cityConfig->zipNotInListTitle($this->storeId);
    }

    public function cityOptionTitle()
    {
        return $this->cityConfig->cityOptionTitle($this->storeId);
    }

    public function zipOptionTitle()
    {
        return $this->cityConfig->zipOptionTitle($this->storeId);
    }
}
