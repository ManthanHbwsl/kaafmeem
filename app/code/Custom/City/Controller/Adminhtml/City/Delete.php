<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Controller\Adminhtml\City;

use Custom\City\Controller\Adminhtml\City;

class Delete extends City
{
    /**
     * @return void
     */
    public function execute()
    {
        $cityId = (int) $this->getRequest()->getParam('id');

        if ($cityId) {
            /** @var $cityModel \Custom\City\Model\City */
            $cityModel = $this->_cityFactory->create();
            $cityModel->load($cityId);

            // Check this city exists or not
            if (!$cityModel->getId()) {
                $this->messageManager->addError(__('This city no longer exists.'));
            } else {
                try {
                    // Delete city
                    $cityNameModel = $this->cityNameFactory->create();
                    $cityRegions = $cityNameModel->getCollection()
                        ->addFieldToFilter('city_id', $cityId);
                    if(count($cityRegions)  > 0 ) {
                        foreach ($cityRegions as $cityRegion) {
                            $cityRegion->delete();
                        }
                    }
                    $cityModel->delete();
                    $this->messageManager->addSuccess(__('The city has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $cityModel->getId()]);
                }
            }
        }
    }
}