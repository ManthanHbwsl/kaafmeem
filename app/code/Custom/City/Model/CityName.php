<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Model;
 
use Magento\Framework\Model\AbstractModel;
 
class CityName extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Custom\City\Model\Resource\CityName');
    }
}