<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\City;

use Custom\City\Controller\Adminhtml\City;

class Save extends City
{
    /**
     * @return void
     */
    public function execute()
    {
        $isPost = $this->getRequest()->isPost();

        if ($isPost) {
            $cityId = 0;
            $cityModel = $this->_cityFactory->create();
            $formData = $this->getRequest()->getParam('city');
            if (isset($formData['id'])) {
                $cityId = $formData['id'];
            }
            $cityModels = $cityModel->getCollection()
                ->addFieldToFilter('city', $formData['city'])
                ->addFieldToFilter('state_id', $formData['state_id'])
                ->addFieldToFilter('country_id', $formData['country_id']);
            if ($cityId > 0) {
                $cityModels = $cityModels->addFieldToFilter('id', array('neq' => $cityId));
            }
            if ($cityModels->count() > 0) {
                $this->messageManager->addError(
                    __('City') . ' <b>"' . $formData['city'] . '"</b> ' . __('is already exist in selected state.')
                );
                $this->_getSession()->setFormData($formData);
                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    if ($cityId > 0) {
                        $this->_redirect('*/*/edit', ['id' => $cityId, '_current' => true]);
                    } else {
                        $this->_redirect('*/*/new');
                    }
                    return;
                }
                if ($cityId > 0) {
                    // Go to grid page
                    $this->_redirect('*/*/edit', ['id' => $cityId]);
                } else {
                    $this->_redirect('*/*/new');
                }
                return;
            }
            if ($cityId > 0) {
                $cityModel->load($cityId);
            } else {
                $formData['created_at'] = date('Y-m-d');
            }
            $cityModel->setData($formData);

            try {
                // Save city
                $cityModel->save();
                $formData['city_name']['en_US'] = $cityModel->getCity();
                if (isset($formData['city_name']) && count($formData['city_name']) > 0) {
                    foreach ($formData['city_name'] as $key => $locale) {
                        $data = [
                            'city_id' => $cityModel->getId(),
                            'locale' => $key,
                            'name' => $locale
                        ];
                        $cityNameModel = $this->cityNameFactory->create();
                        $collection = $cityNameModel->getCollection()
                            ->addFieldToFilter('city_id', $cityModel->getId())
                            ->addFieldToFilter('locale', $key);
                        if ($collection->getSize() > 0) {
                            $collection = $collection->getFirstItem();
                            $cityNameModel->load($collection->getId());
                            if ($locale == null) {
                                $cityNameModel->delete();
                                continue;
                            }

                            $cityNameModel->setName($locale);
                        } else {
                            $cityNameModel->setData($data);
                        }
                        try {
                            if ($locale == null) {
                                continue;
                            }
                            // Save city names
                            $cityNameModel->save();
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
                // Display success message$this->messageManager->addSuccess(__('The city has been saved.'));
                $this->messageManager->addSuccess(__('The city has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $cityModel->getId(), '_current' => true]);
                    return;
                }
                $this->_getSession()->setFormData(null);
                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['id' => $cityId]);
        }
    }
}