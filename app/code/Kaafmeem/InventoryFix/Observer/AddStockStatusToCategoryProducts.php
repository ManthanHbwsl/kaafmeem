<?php
declare(strict_types=1);

namespace Kaafmeem\InventoryFix\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class AddStockStatusToCategoryProducts implements ObserverInterface
{
    /**
     * Observer for catalog_block_product_list_collection
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $event = $observer->getEvent();
        $productCollection = $event->getCollection();
        
        if ($productCollection->getFlag('stock_processed')) {
            return;
        }

        $productCollection->setFlag('product_list_collection', true);
    }
}
