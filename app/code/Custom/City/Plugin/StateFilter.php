<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Plugin;

use Magento\Directory\Model\ResourceModel\Region\Collection;

class StateFilter
{
    public function afterToOptionArray(Collection $subject, $options)
    {
        $allOptions = [];
        foreach ($options as $option) {
            if (isset($option['label'])) {
                $option['label'] = __($option['label']);
            }
            $allOptions[] = $option;
        }
        return (count($allOptions) > 0) ? $allOptions : $options;
    }
}