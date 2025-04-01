<?php
namespace Magecomp\Customredirect\Plugin;

class Cartplugin 
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
	
		
	public function beforeAddProduct($subject, $productInfo, $requestInfo = null)
	{
		if($this->helperdata->isCartEnabled() && $this->helperdata->isCustomerApply()) // Check Add To Cart Enable
		{	
			$cartrtnurl = $this->helperdata->getCartRedirectionUrl();	
			if($cartrtnurl != '' && isset($cartrtnurl))
			{
				$accUrl = $this->_url->getUrl($cartrtnurl);
				$this->request->setParam('return_url', $accUrl);
			}
		}       
        return [$productInfo, $requestInfo];
    }
}