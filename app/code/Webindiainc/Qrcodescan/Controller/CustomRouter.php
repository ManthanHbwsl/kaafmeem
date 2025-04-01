<?php

namespace Webindiainc\Qrcodescan\Controller;

class CustomRouter implements \Magento\Framework\App\RouterInterface
{
   protected $actionFactory;
   protected $_response;
   
   public function __construct(
       \Magento\Framework\App\ActionFactory $actionFactory,
       \Magento\Framework\App\ResponseInterface $response
   ) {
       $this->actionFactory = $actionFactory;
       $this->_response = $response;
   }
   
   public function match(\Magento\Framework\App\RequestInterface $request)
   {
		$identifier = trim($request->getPathInfo(), '/');

		if(strpos($identifier, 'order/view/') !== false) {
			$identifierArray = explode('order/view/', $identifier);
			if( isset($identifierArray[1]) && $identifierArray[1] != '' ) {
				$order_increment_id = $identifierArray[1];
				$request->setModuleName('order')->setControllerName('index')->setActionName('index')->setParam('view', $order_increment_id);
			} else {
				$request->setModuleName('order')->setControllerName('index')->setActionName('index')->setParam('view', $order_increment_id);
			}
		}
		return $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
   }
}