<?php
namespace Humcommerce\FixModuleConflict\Observer;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Event\ObserverInterface;

class OrderPlaceAfter implements ObserverInterface {

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    
    public function __construct(
        DataPersistorInterface $dataPersistor,
    ) { 
        $this->dataPersistor = $dataPersistor;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) { 
        $earningProcessor = false;
        $earningProcessor = $this->dataPersistor->get('earning_processor');
        if($earningProcessor){
            $this->dataPersistor->clear('earning_processor');
        }
        $orderStateChanged = false;
        $orderStateChanged = $this->dataPersistor->get('order_state_change');
        if($orderStateChanged){
            $this->dataPersistor->clear('order_state_change');
        }
    }
}