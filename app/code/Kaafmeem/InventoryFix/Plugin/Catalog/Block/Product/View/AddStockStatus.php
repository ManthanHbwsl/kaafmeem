<?php
declare(strict_types=1);

namespace Kaafmeem\InventoryFix\Plugin\Catalog\Block\Product\View;

use Kaafmeem\InventoryFix\Model\InventoryStock\StatusResolver;
use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Model\Product;
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

    /**
     * @param View $subject
     * @param Product $result
     * @return Product
     */
    public function afterGetProduct(View $subject, Product $result): Product
    {
        if ($result->getData('stock_processed')) {
            return $result;
        }

        if ($result->getTypeId() === Configurable::TYPE_CODE) {
            $isSalable = false;

            foreach ($result->getTypeInstance()->getUsedProductCollection($result) as $child) {
                if ($this->statusResolver->isSalable($child)) {
                    $isSalable = true;
                }
            }
        } else {
            $isSalable = $this->statusResolver->isSalable($result);
        }

        if ($isSalable !== null && !$isSalable) {
            $result->setData('salable', false);
            $result->setData('is_salable', false);
        }

        $result->setData('stock_processed', true);

        return $result;
    }
}
