<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\City;

use Custom\City\Controller\Adminhtml\City;

class MassDelete extends City
{
    /**
     * @return void
     */
    public function execute()
    {
        // Get IDs of the selected city
        $cityIds = $this->getRequest()->getParam('city');

        foreach ($cityIds as $cityId) {
            try {
                /** @var $cityModel \Custom\City\Model\City */
                $cityModel = $this->_cityFactory->create();
                $cityNameModel = $this->cityNameFactory->create();
                $cityRegions = $cityNameModel->getCollection()
                    ->addFieldToFilter('city_id', $cityId);
                if (count($cityRegions) > 0) {
                    foreach ($cityRegions as $cityRegion) {
                        $cityRegion->delete();
                    }
                }
                $cityModel->load($cityId)->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        if (count($cityIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($cityIds))
            );
        }

        $this->_redirect('*/*/index');
    }
}