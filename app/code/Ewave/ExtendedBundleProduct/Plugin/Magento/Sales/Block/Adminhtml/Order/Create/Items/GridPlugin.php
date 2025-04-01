<?php
declare(strict_types=1);

namespace Ewave\ExtendedBundleProduct\Plugin\Magento\Sales\Block\Adminhtml\Order\Create\Items;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid;

class GridPlugin
{
    /**
     * @var StockStateInterface
     */
    private $stockState;

    /**
     * @param StockStateInterface $stockState
     */
    public function __construct(StockStateInterface $stockState)
    {
        $this->stockState = $stockState;
    }


    /**
     * @param Grid $subject
     * @param callable $proceed
     * @return Item[]
     */
    public function aroundGetItems(Grid $subject, callable $proceed): array
    {
        $items = $subject->getParentBlock()->getItems();
        $oldSuperMode = $subject->getQuote()->getIsSuperMode();
        $subject->getQuote()->setIsSuperMode(false);
        foreach ($items as $item) {
            // To dispatch inventory event sales_quote_item_qty_set_after, set item qty
            $item->setQty($item->getQty());

            if (!$item->getMessage()) {
                //Getting product ids for stock item last quantity validation before grid display
                $stockItemToCheck = [];

                $childItems = $item->getChildren();
                if (count($childItems)) {
                    foreach ($childItems as $childItem) {
                        //Detect and replace configuration products to simple products
                        if ($childItem->getProductType() === 'configurable') {
                            $stockItemToCheck[] = $childItem->getOptionByCode('simple_product')->getProductId();
                        } else {
                            $stockItemToCheck[] = $childItem->getProduct()->getId();
                        }
                    }
                } else {
                    $stockItemToCheck[] = $item->getProduct()->getId();
                }

                foreach ($stockItemToCheck as $productId) {
                    $check = $this->stockState->checkQuoteItemQty(
                        $productId,
                        $item->getQty(),
                        $item->getQty(),
                        $item->getQty(),
                        $subject->getQuote()->getStore()->getWebsiteId()
                    );
                    $item->setMessage($check->getMessage());
                    $item->setHasError($check->getHasError());
                }
            }

            if ($item->getProduct()->getStatus() == ProductStatus::STATUS_DISABLED) {
                $item->setMessage(__('This product is disabled.'));
                $item->setHasError(true);
            }
        }
        $subject->getQuote()->setIsSuperMode($oldSuperMode);
        return $items;
    }
}
