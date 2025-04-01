<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Model\CityFactory;
use Custom\City\Model\CityNameFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Custom\City\Model\Resource\State\Collection as StateCollection;
use Custom\City\Model\Resource\City\Collection as CityCollection;

abstract class City extends Action
{
    /** @var Registry  */
    protected $_coreRegistry;

   /** @var PageFactory  */
    protected $_resultPageFactory;

    /** @var CityFactory  */
    protected $_cityFactory;

    /** @var DirectoryList  */
    protected $directoryList;

    /** @var Csv  */
    protected $csv;

    /** @var StateCollection  */
    protected $stateCollection;

    /** @var CityCollection  */
    protected $cityCollection;

    /** @var CityNameFactory  */
    protected $cityNameFactory;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CityFactory $cityFactory,
        DirectoryList $directoryList,
        Csv $csv,
        StateCollection $stateCollection,
        CityCollection $cityCollection,
        CityNameFactory $cityNameFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_cityFactory = $cityFactory;
        $this->directoryList = $directoryList;
        $this->csv = $csv;
        $this->stateCollection = $stateCollection;
        $this->cityCollection = $cityCollection;
        $this->cityNameFactory = $cityNameFactory;
    }

    /**
     * City access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_City::manage_cities');
    }
}
 