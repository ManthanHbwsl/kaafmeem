<?php
namespace Webindiainc\Qrcodescan\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;


class Index extends \Magento\Framework\App\Action\Action
{

	protected $resultPageFactory;
    protected $resultFactory;
    protected $uploaderFactory;
    protected $filesystem;
    protected $resultJsonFactory;
    protected $filestackimageFactory;
    protected $_objectManager;


    protected $_pageFactory;

    public function __construct(
		Context $context,
		PageFactory $pageFactory,
		ResultFactory $resultFactory,
		UploaderFactory $uploaderFactory,
		Filesystem $filesystem,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\ObjectManagerInterface $objectManager
	) {
		return parent::__construct($context);
       	$this->resultPageFactory = $pageFactory;
        $this->resultFactory = $resultFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_objectManager = $objectManager;
    }

    public function execute() {
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
		
		$order_inc_id = $this->getRequest()->getParam('view'); //get order_increment_id
		$title = __("VAT Invoice") . ' ' . $order_inc_id;
		$page->getConfig()->getTitle()->set($title);
        return $page;
    }

}