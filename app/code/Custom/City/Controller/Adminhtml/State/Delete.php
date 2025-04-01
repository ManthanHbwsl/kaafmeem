<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\State;

use Custom\City\Controller\Adminhtml\State;

class Delete extends State
{
    /**
     * @return void
     */
    public function execute()
    {
        $stateId = (int)$this->getRequest()->getParam('id');

        if ($stateId) {
            /** @var $stateModel \Custom\City\Model\State */
            $stateModel = $this->_stateFactory->create();
            $stateModel->load($stateId);

            // Check this state exists or not
            if (!$stateModel->getRegionId()) {
                $this->messageManager->addError(__('This state no longer exists.'));
            } else {
                try {
                    /** @var $cityModel \Custom\City\Model\City */
                    $stateLocales = $this->statelocale->getCollection()
                        ->addFieldToFilter('region_id', $stateId);
                    if ($stateLocales->count() > 0) {
                            $connection = $this->resourceConnection->getConnection();
                            $connection->rawQuery(
                                "Delete FROM directory_country_region_name where region_id=" . $stateId
                            );
                    }
                    // Delete state
                    $stateModel->delete();
                    $this->messageManager->addSuccess(__('The state has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $stateModel->getRegionId()]);
                }
            }
        }
    }
}