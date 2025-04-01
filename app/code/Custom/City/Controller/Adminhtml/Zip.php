<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Model\ZipFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Custom\City\Model\Resource\City\Collection as CityCollection;
use Custom\City\Model\Resource\Zip\Collection as ZipCollection;
use Custom\City\Model\Resource\State\Collection as StateCollection;

abstract class Zip extends Action
{
    /** @var Registry */
    protected $_coreRegistry;

    /** @var PageFactory */
    protected $_resultPageFactory;

    /** @var ZipFactory */
    protected $_zipFactory;

    /** @var DirectoryList */
    protected $directoryList;

    /** @var Csv */
    protected $csv;

    /** @var CityCollection */
    protected $cityCollection;

    /** @var ZipCollection */
    protected $zipCollection;

    /** @var StateCollection */
    protected $stateCollection;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ZipFactory $zipFactory,
        DirectoryList $directoryList,
        Csv $csv,
        CityCollection $cityCollection,
        ZipCollection $zipCollection,
        StateCollection $stateCollection
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_zipFactory = $zipFactory;
        $this->directoryList = $directoryList;
        $this->csv = $csv;
        $this->cityCollection = $cityCollection;
        $this->zipCollection = $zipCollection;
        $this->stateCollection = $stateCollection;
    }

    /**
     * City access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_City::manage_zips');
    }
}
 