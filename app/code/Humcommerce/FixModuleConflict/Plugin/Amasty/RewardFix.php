<?php
declare(strict_types=1);

namespace Humcommerce\FixModuleConflict\Plugin\Amasty;


use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\Rewards\Api\Data\SalesQuote\EntityInterface;
use Amasty\Rewards\Api\Data\SalesQuote\OrderInterface;
use Amasty\Rewards\Model\Config;
use Amasty\Rewards\Model\Order\EarningProcessor;
use Amasty\Rewards\Model\Repository\StatusHistoryRepository;
use Amasty\Rewards\Model\ResourceModel\StatusHistory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order as SalesOrder;
use Magento\Sales\Model\ResourceModel\Order as SalesOrderResource;
use Magento\Framework\Module\Manager;



/**
 * Class RewardFix
 */
class RewardFix
{
    /**
     * @var Config
     */
    private $rewardsConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var EarningProcessor
     */
    private $earningProcessor;

    /**
     * @var StatusHistoryRepository
     */
    private $statusHistoryRepository;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    
    /**
     * @var Manager
     */
    protected $_moduleManager;

    /**
     * @var string
     */
    private $allowEarningProcessor;
    
    /**
     * RewardFix constructor.
     *
     * @param DataPersistorInterface $dataPersistor
     * @param Config $rewardsConfig
     * @param RequestInterface $request
     * @param EarningProcessor $earningProcessor
     * @param StatusHistoryRepository $statusHistoryRepository
     * @param Manager $moduleManager
     */
    public function __construct(
        DataPersistorInterface $dataPersistor,
        Config $rewardsConfig,
        RequestInterface $request,
        EarningProcessor $earningProcessor,
        StatusHistoryRepository $statusHistoryRepository,
        Manager $moduleManager
    )
    {
        $this->dataPersistor = $dataPersistor;
        $this->rewardsConfig = $rewardsConfig;
        $this->request = $request;
        $this->earningProcessor = $earningProcessor;
        $this->statusHistoryRepository = $statusHistoryRepository;
        $this->_moduleManager = $moduleManager;
    }


    /**
     * @param SalesOrderResource $subject
     * @param SalesOrderResource $result
     * @param AbstractModel|SalesOrder $order
     * @return SalesOrderResource
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(
        SalesOrderResource $subject,
        SalesOrderResource $result,
        AbstractModel $order
    ): SalesOrderResource {
        $orderStateChanged = $this->dataPersistor->get('order_state_change');
        $earningProcessor =  $this->dataPersistor->get('earning_processor');
        if(($orderStateChanged || !$this->_moduleManager->isEnabled('Ebizmarts_MailChimp')) && !$earningProcessor ){
            $this->allowEarningProcessor = true;
            $this->dataPersistor->clear('order_state_change');
            $this->dataPersistor->set('earning_processor', true);
        }

        if ($this->request->getParam('creditmemo')
            || !$this->rewardsConfig->isEnabled()
            || !$this->canEarn($order)
            || ($order->getData(EntityInterface::POINTS_EARN) !== null)
        ) {
            return $result;
        }

        $this->allowEarningProcessor = false;
        $this->earningProcessor->process($order);
        

        return $result;
    }

    /**
     * Return true if can earn rewards by MONEY_SPENT_ACTION or ORDER_COMPLETED_ACTION actions
     *
     * @param SalesOrder $orderModel
     * @return bool
     * @throws LocalizedException
     */
    private function canEarn(SalesOrder $orderModel): bool
    {
        return $orderModel->getCustomerId()
            && $this->checkOrderStateAndOrderProcessedStatus($orderModel)
            && $this->request->getActionName() !== 'addComment'
            && $this->isPossibleEarningByCustomerStatus((int)$orderModel->getCustomerId(), $orderModel->getCreatedAt())
            && !($this->rewardsConfig->isDisabledEarningByRewards($orderModel->getStoreId())
                && $orderModel->getData(EntityInterface::POINTS_SPENT))
            && !$orderModel->getTotalRefunded();
    }

    /**
     * @param int $customerId
     * @param string $creationDate
     * @return bool
     * @throws LocalizedException
     */
    private function isPossibleEarningByCustomerStatus(int $customerId, string $creationDate): bool
    {
        $result = false;
        if ($customerId) {
            $statusEntity = $this->statusHistoryRepository->getByCustomerIdAndDate($customerId, $creationDate);
            if ($statusEntity && $statusEntity->getAction() !== StatusHistory::EXCLUDE_ACTION) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @param SalesOrder $orderModel
     * @return bool
     */
    private function checkOrderStateAndOrderProcessedStatus(SalesOrder $orderModel): bool
    {
        $result = false;
        if ($orderModel->getState() === SalesOrder::STATE_COMPLETE
            && ($orderModel->getData(OrderInterface::ORDER_PROCESSED_ATTRIBUTE)
                !== OrderInterface::ORDER_PROCESSED_STATUS)
            && $this->allowEarningProcessor
        ) {
            $result = true;
        }

        return $result;
    }
}
