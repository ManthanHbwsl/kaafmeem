<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\State;

use Custom\City\Controller\Adminhtml\State;

class Grid extends State
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}