<?php
declare(strict_types=1);

namespace Humcommerce\FixSorting\Model\DataMapper;

use Humcommerce\FixSorting\Model\ResourceModel\Inventory;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Stock for mapping
 */
class Stock
{
    /**
     * @var Inventory
     */
    private $inventory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Stock constructor.
     * @param Inventory $inventory
     * @param StoreManagerInterface $storeManager
     * @noinspection PhpUnused
     */
    public function __construct(
        Inventory $inventory,
        StoreManagerInterface $storeManager
    ) {
        $this->inventory = $inventory;
        $this->storeManager = $storeManager;
    }

    /**
     * Map the attribute
     *
     * @param mixed $entityId
     * @param mixed $storeId
     * @return int[]
     * @throws NoSuchEntityException
     */
    public function map($entityId, $storeId): array
    {
        $sku = $this->inventory->getSkuRelation((int)$entityId);

        if (!$sku) {
            return ['out_of_stock_last' => 1];
        }

        $value = $this->inventory->getStockStatus(
            $sku,
            $this->storeManager->getStore($storeId)->getWebsite()->getCode()
        );

        return ['out_of_stock_last' => $value];
    }
}
