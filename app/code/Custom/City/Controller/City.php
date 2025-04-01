<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Model\CityFactory;
use Magento\Framework\Locale\Resolver;
use Custom\City\Model\CityNameFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Exception;
use Psr\Log\LoggerInterface;

abstract class City extends Action
{
    /** @var PageFactory */
    protected $_pageFactory;

    /** @var CityFactory */
    protected $_cityFactory;

    /** @var Resolver */
    private $store;

    /** @var  CityNameFactory */
    private $cityNameFactory;

    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var LoggerInterface  */
    protected $logger;

    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        CityFactory $cityFactory,
        JsonFactory $resultJsonFactory,
        Resolver $store,
        CityNameFactory $cityNameFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_cityFactory = $cityFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->store = $store;
        $this->cityNameFactory = $cityNameFactory;
        $this->logger = $logger;
    }

    /**
     * Get cities by state or country
     **/
    public function getCitiesByState($countryId, $stateId)
    {
        $cities = array();
        $cities_indexes = array();

        if (empty($countryId) && empty($stateId)) {
            return array('cities' => $cities, 'cities_indexes' => $cities_indexes);
        }

        try {
            $currentLocale = $this->store->getLocale();
            $cities_options = $this->_cityFactory->create()->getCollection()
                ->addFieldToFilter('main_table.status', 1);

            if (!empty($countryId)) {
                $cities_options->addFieldToFilter('main_table.country_id', $countryId);

            }
            if (!empty($stateId)) {
                $cities_options->addFieldToFilter('main_table.state_id', $stateId);
            }

            $cities_options->getSelect()->join(
                ['cn' => $cities_options->getTable('cities_names')],
                "cn.city_id = main_table.id",
                ['cn.name as tname']
            );
            $cities_options->getSelect()->where('cn.locale="' . $currentLocale . '"')->group('cn.city_id')
                ->order('cn.name ASC');

            if ($cities_options->count() > 0) {
                foreach ($cities_options as $city) {
                    $cities[] = __($city->getTname());
                    $cities_indexes[] = $city->getTname();
                }
            }
        } catch (Exception $exception) {
            $this->logger->error('Error in cities loading: ' . $exception->getMessage());
            return array('cities' => $cities, 'cities_indexes' => $cities_indexes);
        }

        return array('cities' => $cities, 'cities_indexes' => $cities_indexes);
    }
}
