<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile ElasticSuite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCatalog
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2020 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\ElasticsuiteCatalog\Plugin\Indexer\Stock;

use Smile\ElasticsuiteCatalog\Plugin\Indexer\AbstractIndexerPlugin;

/**
 * Stock (CatalogInventory) indexer operations related plugin.
 * Used to index products into ES after their stock information are indexed by legacy Magento CatalogInventory indexer.
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCatalog
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ReindexProductsAfterStockUpdate extends AbstractIndexerPlugin
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * Process row indexation into ES after the precedent stock index
     *
     * @param \Magento\CatalogInventory\Model\Indexer\Stock $subject    The CatalogInventory indexer
     * @param                                              $result
     * @param int[]                                         $productIds The product ids being reindexed
     *
     * @return void
     */
    public function afterExecute(
        \Magento\CatalogInventory\Model\Indexer\Stock $subject,
        $result,
        $productIds
    ) {
        $this->processFullTextIndex($productIds);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * Process row indexation into ES after the precedent stock index
     *
     * @param \Magento\CatalogInventory\Model\Indexer\Stock $subject   The CatalogInventory indexer
     * @param                                              $result
     * @param int                                           $productId The product id being reindexed
     *
     * @return void
     */
    public function afterExecuteRow(
        \Magento\CatalogInventory\Model\Indexer\Stock $subject,
        $result,
        $productId
    ) {
        $this->processFullTextIndex($productId);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * Process list indexation into ES after the precedent stock index
     *
     * @param \Magento\CatalogInventory\Model\Indexer\Stock $subject    The CatalogInventory indexer
     * @param                                              $result
     * @param int[]                                         $productIds The product ids being reindexed
     *
     * @return void
     */
    public function afterExecuteList(
        \Magento\CatalogInventory\Model\Indexer\Stock $subject,
        $result,
        array $productIds
    ) {
        $this->processFullTextIndex($productIds);
    }
}
