<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Controller\Adminhtml\Zip;

use Custom\City\Controller\Adminhtml\Zip;

class Grid extends Zip
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}