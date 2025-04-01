<?php
declare(strict_types=1);

namespace Kaafmeem\InventoryFix\Plugin\ConfigurableProduct;

use Kaafmeem\InventoryFix\Model\InventoryStock\StatusResolver;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class AddStockStatus
{
    /**
     * @var StatusResolver
     */
    private $statusResolver;

    public function __construct(
        StatusResolver $statusResolver
    ) {
        $this->statusResolver = $statusResolver;
    }

    public function afterLoad(Collection $productCollection, Collection $result): Collection
    {
        if (!$productCollection->getFlag('product_children')
            && !$productCollection->getFlag('product_list_collection')
        ) {
            return $result;
        }

        if ($productCollection->getFlag('stock_processed')) {
            return $result;
        }

        $result->setFlag('stock_processed', true);

        foreach ($result->getItems() as $product) {
            if ($product->getTypeId() === Configurable::TYPE_CODE) {
                $isSalable = false;

                foreach ($product->getTypeInstance()->getUsedProductCollection($product) as $child) {
                    if ($child->getData('is_salable')) {
                        $isSalable = true;
                        break;
                    }
                }
            } else {
                $isSalable = $this->statusResolver->isSalable($product);
            }

            if ($isSalable !== null && !$isSalable) {
                $product->setData('salable', false);
                $product->setData('is_salable', false);
            }
        }

        return $result;
    }
}
