<?php

/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Custom\City\Model\CityFactory;
use Custom\City\Model\CityNameFactory;
use Psr\Log\LoggerInterface;

class AddDefaultCities implements DataPatchInterface
{
    /** @var CityFactory  */
    private $cityFactory;

    /** @var CityNameFactory  */
    private $cityNameFactory;

    /** @var LoggerInterface  */
    private $logger;

    public function __construct(
        CityFactory $cityFactory,
        CityNameFactory $cityNameFactory,
        LoggerInterface $logger
    ) {
        $this->cityFactory = $cityFactory;
        $this->cityNameFactory = $cityNameFactory;
        $this->logger = $logger;
    }

    public function apply()
    {
        try {
            $cities = $this->cityFactory->create()->getCollection();
            $cities->getSelect()->order('id ASC');
            if ($cities->count() > 0) {
                foreach ($cities as $city) {
                    $data = array('name'=>$city->getCity(), 'locale'=>'en_US', 'city_id'=>$city->getId());
                    $cityModel = $this->cityNameFactory->create();
                    $cityModel->setData($data);
                    try {
                        // Save city names
                        $cityModel->save();
                    }catch (\Exception $e) {
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Cities patch updated error '.$e->getMessage());
        }
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [];
    }
}
