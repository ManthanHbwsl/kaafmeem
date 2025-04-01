<?php

namespace Humcommerce\StockStatusFix\Observer;

use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\ConfigurableProduct\Model\Inventory\ChangeParentStockStatus;
use Magento\Catalog\Model\ProductRepository;

class BackToStock implements ObserverInterface
{

    /**
     * @var StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @var StockRegistryProviderInterface
     */
    protected $stockRegistryProvider;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * @var ChangeParentStockStatus
     */
    protected $changeParentStockStatus;

     /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * BackToStock constructor.
     * @param StockRegistryProviderInterface $stockRegistryProvider
     * @param StockConfigurationInterface $stockConfiguration
     * @param ProductResource $productResource
     * @param ChangeParentStockStatus $changeParentStockStatus
     * @param ProductRepository productRepository
     */
    public function __construct(
        StockConfigurationInterface $stockConfiguration,
        StockRegistryProviderInterface $stockRegistryProvider,
        ProductResource $productResource,
        ChangeParentStockStatus $changeParentStockStatus,
        ProductRepository $productRepository
    ) {
        $this->stockConfiguration = $stockConfiguration;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->productResource = $productResource;
        $this->changeParentStockStatus = $changeParentStockStatus;
        $this->productRepository = $productRepository;
    }

    /**
     * Return creditmemo items qty to stock
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {

        /* @var $creditMemo \Magento\Sales\Model\Order\Creditmemo */
        $creditMemo = $observer->getEvent()->getCreditmemo();
        foreach ($creditMemo->getItems() as $item) {
            /** @var \Magento\Sales\Model\Order\Creditmemo\Item $item */
            if ($item->getBackToStock() && ($orderItem = $item->getOrderItem())) {
                $simpleSku = $orderItem->getData('sku');
                $productTypeId = $orderItem->getData('product_type');
                if ($simpleSku && ($simpleId = $this->productResource->getIdBySku($simpleSku))) {
                    $scopeId = $orderItem->getStore()->getWebsiteId();
                    $stockItem = $this->stockRegistryProvider->getStockItem($simpleId, $scopeId);
                    if ($stockItem->getItemId() && $this->stockConfiguration->isQty($productTypeId)) {
                        if ($this->stockConfiguration->getCanBackInStock($stockItem->getStoreId()) && $stockItem->getQty()
                            > $stockItem->getMinQty()
                        ) {
                            $stockItem->setIsInStock(true);
                            $stockItem->setStockStatusChangedAutomaticallyFlag(true);
                        }
                        $stockItem->save();
                        $this->changeParentStockStatus->execute([$item->getData('product_id')]);
                    }
                    elseif($productTypeId == 'configurable'){
                        $configurableProduct = $this->productRepository->getById($item->getData('product_id'));
                        $stockStatus = $configurableProduct->getQuantityAndStockStatus();
                        if(isset($stockStatus['is_in_stock']) && !$stockStatus['is_in_stock']){
                            $configurableProduct->setQuantityAndStockStatus(['is_in_stock' => true]);
                            $configurableProduct->save();
                        }
                    }
                }
            }
        }
    }
}
