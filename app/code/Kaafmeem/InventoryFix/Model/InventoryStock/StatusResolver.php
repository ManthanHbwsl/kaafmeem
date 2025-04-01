<?php
declare(strict_types=1);

namespace Kaafmeem\InventoryFix\Model\InventoryStock;

use Magento\Catalog\Model\Product;
use Magento\Framework\Module\Manager;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class StatusResolver
{
    /**
     * @var GetProductSalableQtyInterface
     */
    private $getProductSalableQty;

    /**
     * @var StockByWebsiteIdResolverInterface
     */
    private $stockResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    private $isSourceItemManagementAllowedForProductType;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var array
     */
    private $storage = [];

    public function __construct(
        GetProductSalableQtyInterface $getProductSalableQty,
        StockByWebsiteIdResolverInterface $stockResolver,
        StoreManagerInterface $storeManager,
        IsSourceItemManagementAllowedForProductTypeInterface $isSourceItemManagementAllowedForProductType,
        Manager $moduleManager
    ) {
        $this->getProductSalableQty = $getProductSalableQty;
        $this->stockResolver = $stockResolver;
        $this->storeManager = $storeManager;
        $this->isSourceItemManagementAllowedForProductType = $isSourceItemManagementAllowedForProductType;
        $this->moduleManager = $moduleManager;
    }

    public function isSalable(Product $product): ?bool
    {
        if (!$this->moduleManager->isEnabled('Magento_Inventory')) {
            return true;
        }

        $productId = $product->getId();
        if (isset($this->storage[$productId])) {
            return $this->storage[$productId];
        }

        $stockId = $this->stockResolver->execute((int)$this->storeManager->getStore()->getWebsiteId())->getStockId();

        if ($this->isSourceItemManagementAllowedForProductType->execute($product->getTypeId()) === false) {
            return null;
        }

        $salableQty = $this->getProductSalableQty->execute((string)$product->getSku(), $stockId);

        $this->storage[$productId] = $salableQty > 0.0;

        return $this->storage[$productId];
    }
}
