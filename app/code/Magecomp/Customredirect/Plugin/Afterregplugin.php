<?php
namespace Magecomp\Customredirect\Plugin;

use \Magento\Customer\Controller\Account\CreatePost;

class Afterregplugin 
{
	protected $_url;
	protected $request;
	protected $helperdata;
	
    public function __construct(\Magento\Framework\UrlInterface $url,
	\Magento\Framework\App\Request\Http $request,
	\Magecomp\Customredirect\Helper\Data $helperdata)
    {
		$this->_url = $url;
		$this->request = $request;
		$this->helperdata = $helperdata;      
    }
	
	public function afterExecute(CreatePost $customerAccountController,$resultRedirect)
	{
		if($this->helperdata->isRegisterEnabled() && $this->helperdata->isCustomerRegisterApply()) // Check Register Enable
		{	
			$regrtnurl = $this->helperdata->getRegRedirectionUrl();	
			if($regrtnurl != '' && isset($regrtnurl))
			{
				$accUrl = $this->_url->getUrl($regrtnurl);
				$resultRedirect->setUrl($accUrl);
			}
		}
		
		return $resultRedirect;
	}
}