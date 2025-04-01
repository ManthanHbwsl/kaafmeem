<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml\City;

use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends Action
{
    const FILES = ['city' => 'citiesimport.csv', 'state' => 'statesimport.csv', 'zip' => 'zipsimport.csv'];
    const REDIRECTS = ['city' => 'city/city/import', 'state' => 'city/state/import', 'zip' => 'city/zip/import'];

    /** @var RawFactory */
    protected $resultRawFactory;

    /** @var FileFactory */
    protected $fileFactory;

    /** @var DirectoryList */
    protected $directory;

    public function __construct(
        RawFactory $resultRawFactory,
        FileFactory $fileFactory,
        Context $context,
        DirectoryList $directory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->fileFactory = $fileFactory;
        $this->directory = $directory;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!isset($params['file']) || !array_key_exists($params['file'], self::FILES)) {
            $resultRedirect->setPath('city/city/import');
            return $resultRedirect;
        }
        try {
            $fileName = self::FILES[$params['file']];
            $file = $this->directory->getPath("var") . "/import/" . $fileName;
            return $this->fileFactory->create(
                $fileName,
                @file_get_contents($file)
            );
        } catch (\Exception $exception) {
            $this->messageManager->addError(__('Something went wrong while downloading file!'));
        }
        $resultRedirect->setPath(self::REDIRECTS[$params['file']]);
        return $resultRedirect;
    }
}