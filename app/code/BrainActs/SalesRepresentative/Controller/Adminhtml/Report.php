<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml;

abstract class Report extends AbstractReport
{
    /**
     * @return $this|\BrainActs\SalesRepresentative\Controller\Adminhtml\AbstractReport
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(__('Sales'), __('Sales'));
        return $this;
    }
}
