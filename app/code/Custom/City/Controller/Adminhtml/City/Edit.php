<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Controller\Adminhtml\City;

use Custom\City\Controller\Adminhtml\City;

class Edit extends City
{

    /**
     * @return void
     */
    public function execute()
    {
        $cityId = $this->getRequest()->getParam('id');
        /** @var \Custom\City\Model\City $model */
        $model = $this->_cityFactory->create();
        $localesData = [];
        if ($cityId) {
            $model->load($cityId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This city no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            $cityRegions = $this->cityNameFactory->create()->getCollection()
                ->addFieldToFilter('city_id', $cityId)
                ->addFieldToFilter('locale', array('neq' => 'en_US'));
            if (count($cityRegions)  > 0 ) {
                foreach ($cityRegions as $cityRegion) {
                    $localesData[$cityRegion->getLocale()] = $cityRegion->getName();
                }
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getCityData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        if (count($localesData) > 0) {
            $model->setData(array_merge($model->getData(), $localesData));
        }
        $this->_coreRegistry->register('city_city', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_City::city');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Cities'));

        return $resultPage;
    }
}