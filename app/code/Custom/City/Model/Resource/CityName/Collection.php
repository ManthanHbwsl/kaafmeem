<?php

namespace Custom\City\Model\Resource\CityName;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Custom\City\Model\CityName',
            'Custom\City\Model\Resource\CityName'
        );
    }
}