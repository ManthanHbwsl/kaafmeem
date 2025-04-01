<?php

namespace Evincemage\Trackorder\Controller\Order;

use Magento\Framework\Controller\ResultFactory;

class Shipment extends \Magento\Framework\App\Action\Action
{
  protected $resultPageFactory;

  protected $_orderCollectionFactory;

  protected $_order;

  public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_url = $context->getUrl();
        $this->_order = $order;
        parent::__construct($context);
    }
    public function execute()
    {
        $track = $this->getRequest()->getPost('track');

          if (!empty($track['order_id']) && !empty($track['customer_email'])) {

              $trackorder_id = $track['order_id'];

              $trackuser_email = $track['customer_email'];

              $order_detail = $this->_order->loadByIncrementId($trackorder_id);

              if($order_detail['customer_email'] == $trackuser_email){

                $order_method = $order_detail->getTracksCollection();  
                
                $trackData = array();
                $html = $orderifohtml = "";

                $orderifohtml = "<ul><li><span>". __('Order ID #') ."<b><p id='orderid'> ".$trackorder_id." </p></b></span></li><li><span>". __('Order current status:') ."<b><p id='order_sta'> ".$order_detail['status']." </p></b></span></li><li><span>" . __('To check your order detail please ') . "<b><p id= 'orurl'><a href='" . $this->_url->getUrl('sales/order/view/') .'order_id/'. $order_detail['entity_id'] . "'> " . __(' Click here') . " </a></p></b></span></li></ul>";

                if(count($order_method)){
                  foreach ($order_method as $trackingData) {
                    $html .="<tr><td>".$trackingData->getTitle()."</td><td>".$trackingData->getTrackNumber()."</td></tr>";   
                  }
                  $success_message = __('We found tracking information for order #'.$order_detail['increment_id'].'.');
                } else {
                  $success_message = __('Order #'. $order_detail['increment_id'] .' Not shipped yet.');
                }
                $response  = array('trackData'=>$html, 'status' => true, 'emessage' => $success_message, 'orderdetailhtml' => $orderifohtml);                
              } else{
                  $error_message = __('No record found for the given details please check your Email or Order Id.');
                  $response =  array('status' => false, 'emessage' => $error_message);
              }

              $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

              $resultJson->setData($response);

              return $resultJson;

        }

    }

}
