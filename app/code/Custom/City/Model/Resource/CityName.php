<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Model\Resource;
 
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class CityName extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('cities_names', 'id');
    }
}