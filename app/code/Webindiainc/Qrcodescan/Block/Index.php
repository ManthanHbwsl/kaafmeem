<?php

namespace Webindiainc\Qrcodescan\Block;

class Index extends \Magento\Checkout\Block\Onepage\Success
{
	protected $orderRepository;
    protected $renderer;

    public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Sales\Model\Order\Config $orderConfig,
		\Magento\Framework\App\Http\Context $httpContext,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\Sales\Model\Order\Address\Renderer $renderer,
		array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->renderer = $renderer;
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
    }


	/* public function _prepareLayout() {  
	   $this->pageConfig->getTitle()->set(__('VAT Invoice'));  
		return parent::_prepareLayout();  
	} */

    public function getOrder($id) {
        return $this->orderRepository->get($id);
    }

    public function getFormatedAddress($address) {
        return $this->renderer->format($address, 'html');
    }

    public function getPaymentMethodtitle($order) {
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        return $method->getTitle();
    }

}