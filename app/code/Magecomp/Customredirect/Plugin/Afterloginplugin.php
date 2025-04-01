<?php
namespace Magecomp\Customredirect\Plugin;

use \Magento\Customer\Controller\Account\LoginPost;

class Afterloginplugin
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

	public function afterExecute(LoginPost $customerAccountLoginController,$resultRedirect)
	{
		if($this->helperdata->isLoginEnabled() && $this->helperdata->isCustomerLoginApply()) // Check Login Enable
		{
			$loginrtnurl = $this->helperdata->getLoginRedirectionUrl();
			if($loginrtnurl != '' && isset($loginrtnurl))
			{
				$accUrl = $this->_url->getUrl($loginrtnurl);
				$resultRedirect->setUrl($accUrl);
			}
		}

		return $resultRedirect;
	}
}
