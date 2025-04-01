<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\State;

use Custom\City\Controller\Adminhtml\State;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\Response\Http\FileFactory;

class Import extends State
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->downloadSampleZipsAction();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_City::state_import');
        $resultPage->getConfig()->getTitle()->prepend(__('Import States'));

        return $resultPage;
    }

    public function downloadSampleZipsAction()
    {
        $dir = $this->directoryList;
        // let's get the log dir for instance
        $logDir = $dir->getPath(DirectoryList::VAR_DIR);
        $path = $logDir . '/' . 'import';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        //Sample Csv generation Code
        $outputFile = $logDir . '/' . 'import' . '/' . 'statesimport.csv';
        if (!file_exists($outputFile)) {
            $heading = [
                __('State'),
                __('State Code')
            ];
            $handle = fopen($outputFile, 'w');
            fputcsv($handle, $heading);
        }
    }
}