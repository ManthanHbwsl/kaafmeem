<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\State;

use Custom\City\Controller\Adminhtml\State;

class NewAction extends State
{
    /**
     * Create new State action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
 