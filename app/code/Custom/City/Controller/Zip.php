<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Model\ZipFactory;
use Custom\City\Model\CityFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;

abstract class Zip extends Action
{
    /** @var PageFactory */
    protected $_pageFactory;

    /** @var ZipFactory */
    protected $_zipFactory;

    /** @var Resolver */
    private $store;

    /** @var CityFactory */
    protected $_cityFactory;

    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CityFactory $cityFactory,
        ZipFactory $zipFactory,
        JsonFactory $resultJsonFactory,
        Resolver $store,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_zipFactory = $zipFactory;
        $this->_cityFactory = $cityFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->store = $store;
        $this->logger = $logger;
    }

    /**
     * Get zip codes by city
     **/
    public function getZipsByCity($city, $countryId, $stateId)
    {
        $zip_codes_options = array();

        if (empty($countryId) && empty($stateId) && empty($city)) {
            return $zip_codes_options;
        }

        try {
            $currentLocale = $this->store->getLocale();
            $cities = $this->_cityFactory->create()->getCollection();
            $cities->getSelect()->join(
                ['cn' => $cities->getTable('cities_names')],
                "cn.city_id = main_table.id",
                ['cn.name as tname']
            );
            $cities->getSelect()->where('cn.locale="' . $currentLocale . '" AND cn.name="' . $city . '"')
                ->group('cn.city_id');
            if (!empty($countryId)) {
                $cities->addFieldToFilter('country_id', $countryId);
            }
            if (!empty($stateId)) {
                $cities->addFieldToFilter('state_id', $stateId);
            }
            if ($cities->count() > 0) {
                $city_id = $cities->getFirstItem()->getId();
                $zip_codes = $this->_zipFactory->create()->getCollection()
                    ->addFieldToFilter('status', 1)
                    ->addFieldToFilter('city_id', $city_id);
                if (!empty($countryId)) {
                    $zip_codes->addFieldToFilter('country_id', $countryId);
                }
                if (!empty($stateId)) {
                    $zip_codes->addFieldToFilter('state_id', $stateId);
                }
                $zip_codes->getSelect()->order('id DESC');
                if ($zip_codes->count() > 0) {
                    foreach ($zip_codes as $zip) {
                        $zip_codes_options[] = $zip->getZipName();
                    }
                }
            }
        } catch (Exception $exception) {
            $this->logger->error('Error in zip codes loading: ' . $exception->getMessage());
        }

        return $zip_codes_options;
    }
}
