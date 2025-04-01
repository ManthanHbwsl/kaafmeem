<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\City;

use Custom\City\Controller\Adminhtml\City;
use Magento\Framework\App\Filesystem\DirectoryList;

class Import extends City
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->downloadSampleZipsAction();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_City::city_import');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Cities'));
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
        $outputFile = $logDir . '/' . 'import' . '/' . 'citiesimport.csv';
        if (!file_exists($outputFile)) {
            $heading = [
                __('City'),
                __('State')
            ];
            $handle = fopen($outputFile, 'w');
            fputcsv($handle, $heading);
        }
    }
}