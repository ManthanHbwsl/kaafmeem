<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\City;

use Custom\City\Controller\Adminhtml\City;

class Grid extends City
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}