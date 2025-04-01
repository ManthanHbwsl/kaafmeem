<?php

/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\System\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const XML_PATH_ENABLED = 'city/general/enabled';

    const XML_PATH_CITY_NOT_IN_LIST = 'city/general/citynotinlist';

    const XML_PATH_CITYNOTINLIST_TITLE = 'city/general/citynotinlist_title';

    const XML_PATH_ZIP_NOT_IN_LIST = 'city/general/zipnotinlist';

    const XML_PATH_ZIPNOTINLIST_TITLE = 'city/general/zipnotinlist_title';

    const XML_PATH_IS_STATE_AVAILABLE = 'city/general/is_state_available';

    const XML_PATH_CITY_TITLE = 'city/general/city_title';

    const XML_PATH_ZIP_TITLE = 'city/general/zip_title';

    const XML_PATH_SHIPPING_RATE = 'city/general/shipping_rate';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_ENABLED, $storeId);
    }

    public function isShippingRateEnabled($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_SHIPPING_RATE, $storeId);
    }

    public function cityNotInList($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_CITY_NOT_IN_LIST, $storeId);
    }

    public function cityNotInListTitle($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_CITYNOTINLIST_TITLE, $storeId);
    }

    public function zipNotInList($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_ZIP_NOT_IN_LIST, $storeId);
    }

    public function zipNotInListTitle($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_ZIPNOTINLIST_TITLE, $storeId);
    }

    public function isStateAvailable($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_IS_STATE_AVAILABLE, $storeId);
    }

    public function cityOptionTitle($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_CITY_TITLE, $storeId);
    }

    public function zipOptionTitle($storeId)
    {
        return $this->getConfigValue(self::XML_PATH_ZIP_TITLE, $storeId);
    }

    private function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
