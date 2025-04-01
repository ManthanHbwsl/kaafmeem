<?php

namespace Webindiainc\Qrcodescan\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Amasty\CashOnDelivery\Model\OrderPaymentFeeFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    private $httpContext;
    protected $productRepository;
    protected $_session;
    protected $price_helper;
	protected $orderPaymentFeeFactory;
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\Pricing\Helper\Data
     */
    public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    \Magento\Catalog\Model\ProductRepository $productRepository,
    \Magento\Framework\App\Http\Context $httpContext,
    \Magento\Customer\Model\Session $session,
    \Magento\Framework\Pricing\Helper\Data $price_helper,
    OrderPaymentFeeFactory $orderPaymentFeeFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->httpContext = $httpContext;
        $this->_session = $session;
        $this->price_helper = $price_helper;
        $this->orderPaymentFeeFactory = $orderPaymentFeeFactory;
        parent::__construct($context);
    }

    public function getStoreConfig($storePath) {
        $storeConfig = $this->scopeConfig->getValue($storePath, ScopeInterface::SCOPE_STORE);
        return $storeConfig;
    }

    public function getProductById($id) {
        return $this->productRepository->getById($id);
    }

    public function isLoggedIn() {
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        return $isLoggedIn;
    }

    public function getFormatedPrice($price='')
    {
        return $this->price_helper->currency($price, true, false);
    }
	
	public function getCodFee($order_id)
    {
		$orderPaymentFeeCollection = $this->orderPaymentFeeFactory->create();
        $collection = $orderPaymentFeeCollection->getCollection()->addFieldToFilter('order_id', $order_id)->getFirstItem();
        return $collection->getData();
    }

}
