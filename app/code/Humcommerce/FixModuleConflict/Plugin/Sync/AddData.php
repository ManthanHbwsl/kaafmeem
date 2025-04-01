<?php
declare(strict_types=1);

namespace Humcommerce\FixModuleConflict\Plugin\Sync;

use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Request\DataPersistorInterface;


/**
 * Class AddData
 */
class AddData
{
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * AddData constructor.
     *
     * @param StockDataMapper $stockDataMapper
     * @param Inventory $inventory
     */
    public function __construct(
        OrderFactory $orderFactory,
        DataPersistorInterface $dataPersistor
    )
    {
        $this->orderFactory = $orderFactory;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Add value to datapersister
     *
     */
    public function beforeSaveEcommerceData(
        $subject,
        ...$args
    ){
        if(!empty($args[1]) && $args[2] == \Ebizmarts\MailChimp\Helper\Data::IS_ORDER){
            $order = $this->orderFactory->create()->loadByAttribute('entity_id', $args[1]);
            $orderState = $order->getState();
            $earningProcessor = false;
            $earningProcessor = $this->dataPersistor->get('earning_processor');
            if($orderState == Order::STATE_COMPLETE && !$earningProcessor){
                $this->dataPersistor->set('order_state_change', true);
            }

        }


        return $args;
    }
}
