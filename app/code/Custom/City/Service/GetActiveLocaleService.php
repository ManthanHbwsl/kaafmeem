<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

class GetActiveLocaleService
{
    const DEFAULT_LOCALE = 'en_US';
    const LOCALE_PATH = 'general/locale/code';

    /** @var StoreRepositoryInterface */
    private $storeRepository;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(
        StoreRepositoryInterface $storeRepository,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeRepository = $storeRepository;
        $this->scopeConfig = $scopeConfig;
    }

    public function get()
    {
        $locales = [];
        $stores = $this->storeRepository->getList();
        foreach ($stores as $store) {
            if ($store->getIsActive() == false) {
                continue;
            }
            $locale = $this->scopeConfig->getValue(
                self::LOCALE_PATH,
                ScopeInterface::SCOPE_STORE,
                $store->getStoreId()
            );
            if (!in_array($locale, $locales) && $locale != self::DEFAULT_LOCALE) {
                $locales[] = $locale;
            }
        }
        return $locales;
    }
}