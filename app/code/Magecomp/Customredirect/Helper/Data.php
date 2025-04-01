<?php
namespace Magecomp\Customredirect\Helper;

use Magento\Backend\Model\UrlFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
	protected $_storeManager;
	protected $_customerSession;
	protected $_customerRepositoryInterface;
	
    public function __construct(Context $context,  
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface)
    {
		$this->_storeManager = $storeManager;
		$this->_customerSession = $customerSession; 
		$this->_customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context);
    }

	public function getCurrentStoreInfo()
	{
		return $this->_storeManager->getStore()->getId();
	}
	
	public function isEnabled()
	{
		return (bool) $this->scopeConfig->getValue('customredirec/general/enabled', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
	}
	
	/* Customer Login Section */
	public function isLoginEnabled()
	{
		if($this->isEnabled())
		{
			return (bool) $this->scopeConfig->getValue('customredirec/loginsec/enabled', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
	}
	
	public function isCustomerLoginApply()
	{	
		$gstr = $this->scopeConfig->getValue('customredirec/loginsec/logingroup', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		if(isset($gstr))
		{		
			$logincustomergroup = explode(',',$gstr);
			$cstgid = 0;
			if($this->_customerSession->isLoggedIn()) 
			{
   				$cstgid = $this->_customerSession->getCustomer()->getGroupId();
			}	
			if(in_array($cstgid,$logincustomergroup)) 
			{
				return true;	
			}
			else
			{
				return false;
			}
		}		
		return true;
	}
	
	public function getLoginRedirectionUrl()
	{
		$loginrtype = $this->scopeConfig->getValue('customredirec/loginsec/loginrtype', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		
		if($loginrtype) // Custom Path
		{
			return $this->scopeConfig->getValue('customredirec/loginsec/loginpath', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
		else // CMS PAGE
		{
			return $this->scopeConfig->getValue('customredirec/loginsec/logincmspage', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
	}
	
	/* Customer Logout Section */
	public function isLogoutEnabled()
	{
		if($this->isEnabled())
		{
			return (bool) $this->scopeConfig->getValue('customredirec/logoutsec/enabled', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
	}
	
	public function isCustomerLogoutApply($custId)
	{
		$customer = $this->_customerRepositoryInterface->getById($custId);
		$gstr = $this->scopeConfig->getValue('customredirec/logoutsec/logoutgroup', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());	
		if(isset($gstr))
		{
			$logoutcustomergroup = explode(',',$gstr);
			$cstgid = $customer->getGroupId();	
			if(in_array($cstgid,$logoutcustomergroup)) 
			{
				return true;	
			}
			else
			{
				return false;
			}
		}		
		return true;
	}
	
	public function getLogoutRedirectionUrl()
	{
		$loginrtype = $this->scopeConfig->getValue('customredirec/logoutsec/logoutrtype', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		
		if($loginrtype) // Custom Path
		{
			return $this->scopeConfig->getValue('customredirec/logoutsec/logoutpath', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
		else // CMS PAGE
		{
			return $this->scopeConfig->getValue('customredirec/logoutsec/logoutcmspage', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
	}
	
	/* Cart Register Section */
	public function isRegisterEnabled()
	{
		if($this->isEnabled())
		{
			return (bool) $this->scopeConfig->getValue('customredirec/regsec/enabled', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
	}
	
	public function isCustomerRegisterApply()
	{	
		$gstr = $this->scopeConfig->getValue('customredirec/regsec/reggroup', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		if(isset($gstr))
		{		
			$Cartcustomergroup = explode(',',$gstr);
			$cstgid = 0;
			if($this->_customerSession->isLoggedIn()) 
			{
   				$cstgid = $this->_customerSession->getCustomer()->getGroupId();
			}	
			if(in_array($cstgid,$Cartcustomergroup)) 
			{
				return true;	
			}
			else
			{
				return false;
			}
		}		
		return true;
	}
	
	public function getRegRedirectionUrl()
	{
		$regrtype = $this->scopeConfig->getValue('customredirec/regsec/regrtype', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		
		if($regrtype) // Custom Path
		{
			return $this->scopeConfig->getValue('customredirec/regsec/regpath', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
		else // CMS PAGE
		{
			return $this->scopeConfig->getValue('customredirec/regsec/regcmspage', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
	}
	
	/* Cart Section */
	public function isCartEnabled()
	{
		if($this->isEnabled())
		{
			return (bool) $this->scopeConfig->getValue('customredirec/cartsec/enabled', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
	}
	
	public function isCustomerApply()
	{	
		$gstr = $this->scopeConfig->getValue('customredirec/cartsec/cartgroup', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		if(isset($gstr))
		{		
			$Cartcustomergroup = explode(',',$gstr);
			$cstgid = 0;
			if($this->_customerSession->isLoggedIn()) 
			{
   				$cstgid = $this->_customerSession->getCustomer()->getGroupId();
			}	
			if(in_array($cstgid,$Cartcustomergroup)) 
			{
				return true;	
			}
			else
			{
				return false;
			}
		}		
		return true;
	}
	
	public function getCartRedirectionUrl()
	{
		$cartrtype = $this->scopeConfig->getValue('customredirec/cartsec/cartrtype', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		
		if($cartrtype) // Custom Path
		{
			return $this->scopeConfig->getValue('customredirec/cartsec/cartpath', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
		else // CMS PAGE
		{
			return $this->scopeConfig->getValue('customredirec/cartsec/cartcmspage', ScopeInterface::SCOPE_STORE,$this->getCurrentStoreInfo());
		}
	}
}