<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\State;

use Custom\City\Controller\Adminhtml\State;

class Save extends State
{
    /**
     * @return void
     */
    public function execute()
    {
        $isPost = $this->getRequest()->isPost();
        if ($isPost) {
            $stateId = 0;
            $stateModel = $this->_stateFactory->create();
            $formData = $this->getRequest()->getParam('state');
            if (isset($formData['id'])) {
                $formData['region_id'] = $formData['id'];
            }
            if (isset($formData['id'])) {
                $stateId = $formData['id'];
            }
            $stateModels = $stateModel->getCollection()
                ->addFieldToFilter('default_name', $formData['default_name'])->addFieldToFilter(
                    'country_id',
                    $formData['country_id']
                );
            if ($stateId > 0) {
                $stateModels = $stateModels->addFieldToFilter('region_id', array('neq' => $stateId));
            }
            if ($stateModels->count() > 0) {
                $this->messageManager->addError(
                    __('State') . ' <b>"' . $formData['default_name'] . '"</b> ' . __(
                        'is already exist in selected country.'
                    )
                );
                $this->_getSession()->setFormData($formData);
                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    if ($stateId > 0) {
                        $this->_redirect('*/*/edit', ['id' => $stateId, '_current' => true]);
                    } else {
                        $this->_redirect('*/*/new');
                    }
                    return;
                }
                if ($stateId > 0) {
                    // Go to grid page
                    $this->_redirect('*/*/edit', ['id' => $stateId]);
                } else {
                    $this->_redirect('*/*/new');
                }
                return;
            }
            if ($stateId > 0) {
                $stateModel->load($stateId);
            }
            $stateModel->setData($formData);
            try {
                // Save state
                $stateModel->save();
                $last_id = $stateModel->getId();
                $formData['state_name']['en_US'] = $formData['default_name'];

                if (isset($formData['state_name']) && count($formData['state_name']) > 0) {
                    foreach ($formData['state_name'] as $lang => $name) {
                        try {
                            $stateLocale = $this->statelocale->getCollection()
                                ->addFieldToFilter('region_id', $last_id)
                                ->addFieldToFilter('locale', $lang);
                            if ($stateLocale->count() == 0 && $name != null) {
                                $lang_data = array('locale' => $lang, 'region_id' => $last_id, 'name' => $name);
                                $this->statelocale->setData($lang_data)->save();
                            } else {
                                if ($name != null) {
                                    $connection = $this->resourceConnection->getConnection();
                                    $connection->rawQuery(
                                        "UPDATE directory_country_region_name set name = '" . $name . "' where region_id=" . $last_id . " AND locale='" . $lang . "'"
                                    );
                                } else {
                                    $connection = $this->resourceConnection->getConnection();
                                    $connection->rawQuery(
                                        "Delete FROM directory_country_region_name where region_id=" . $last_id . " AND locale='" . $lang . "'"
                                    );
                                }
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
                // Display success message
                $this->messageManager->addSuccess(__('The state has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $stateModel->getId(), '_current' => true]);
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
            $this->_redirect('*/*/edit', ['id' => $stateId]);
        }
    }
}