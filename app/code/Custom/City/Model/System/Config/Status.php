<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Model\System\Config;
 
use Magento\Framework\Option\ArrayInterface;
 
class Status implements ArrayInterface
{
    const ENABLED  = 1;
    const DISABLED = 0;
 
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::ENABLED => __('Enabled'),
            self::DISABLED => __('Disabled')
        ];
 
        return $options;
    }
}