<?php
namespace Magecomp\Customredirect\Plugin;

use Magento\Customer\Controller\Account\Logout;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

class Aroundlogoutplugin 
{
	protected $_url;
	protected $request;
	protected $helperdata;
	protected $resultredirect;
	protected $session;
	private $cookieMetadataFactory;
	private $cookieMetadataManager;
	protected $_redirect;
	
    public function __construct(\Magento\Framework\UrlInterface $url,
	\Magento\Framework\App\Request\Http $request,
	\Magecomp\Customredirect\Helper\Data $helperdata,
	\Magento\Framework\Controller\Result\Redirect $resultredirect,
	Session $customerSession,
	\Magento\Framework\App\Response\RedirectInterface $redirect)
    {
		$this->_url = $url;
		$this->request = $request;
		$this->helperdata = $helperdata;  
		$this->resultredirect = $resultredirect;  
		$this->session = $customerSession;
		$this->_redirect = $redirect;  
    }
	
	private function getCookieManager()
	{
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = ObjectManager::getInstance()->get(PhpCookieManager::class);
        }
        return $this->cookieMetadataManager;
    }
	
	private function getCookieMetadataFactory()
	{
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = ObjectManager::getInstance()->get(CookieMetadataFactory::class);
        }
        return $this->cookieMetadataFactory;
    }
	
	public function aroundExecute(Logout $subject, $proceed)
	{	
		$lastCustomerId = $this->session->getId();
        $this->session->logout()->setBeforeAuthUrl($this->_redirect->getRefererUrl())
            ->setLastCustomerId($lastCustomerId);
        if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
            $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
            $metadata->setPath('/');
            $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
        }

		// Magecomp Customization (START)
		if($this->helperdata->isLogoutEnabled() && $this->helperdata->isCustomerLogoutApply($lastCustomerId)) // Check Logout Enable
		{
			$logoutrtnurl = $this->helperdata->getLogoutRedirectionUrl();		
			if($logoutrtnurl != '' && isset($logoutrtnurl))
			{
				$this->resultredirect->setPath($logoutrtnurl);
        		return $this->resultredirect;
			}
		}
		// Magecomp Customization (END)
		
		$this->resultredirect->setPath('*/*/logoutSuccess');
        return $this->resultredirect;
	}
}