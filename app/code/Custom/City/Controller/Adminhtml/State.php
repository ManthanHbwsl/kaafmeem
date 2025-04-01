<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Model\StateFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\File\Csv;
use Custom\City\Model\Resource\State\Collection as StateCollection;
use Custom\City\Model\Resource\Statelocale\Collection as StateLocaleCollection;
use Custom\City\Model\Statelocale;
use Magento\Framework\App\ResourceConnection;

abstract class State extends Action
{
    /** @var Registry  */
    protected $_coreRegistry;

    /** @var PageFactory  */
    protected $_resultPageFactory;

    /** @var StateFactory  */
    protected $_stateFactory;

    /** @var ResourceConnection  */
    protected $resourceConnection;

    /** @var Statelocale  */
    protected $statelocale;

    /** @var DirectoryList  */
    protected $directoryList;

    /** @var Csv  */
    protected $csv;

    /** @var StateCollection  */
    protected $stateCollection;

    /** @var StateLocaleCollection  */
    protected $stateLocaleCollection;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        StateFactory $stateFactory,
        DirectoryList $directoryList,
        Csv $csv,
        StateCollection $stateCollection,
        StateLocaleCollection $stateLocaleCollection,
        Statelocale $stateLocale,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_stateFactory = $stateFactory;
        $this->directoryList = $directoryList;
        $this->csv = $csv;
        $this->stateCollection = $stateCollection;
        $this->stateLocaleCollection = $stateLocaleCollection;
        $this->statelocale = $stateLocale;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * State access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_City::manage_states');
    }
}
 