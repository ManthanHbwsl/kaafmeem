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

namespace Smile\ElasticsuiteCatalog\Plugin\Indexer\Price;

use Smile\ElasticsuiteCatalog\Plugin\Indexer\AbstractIndexerPlugin;

/**
 * Price indexer operations related plugin.
 * Used to index products into ES after their price information are indexed by legacy Magento indexer.
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCatalog
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ReindexProductsAfterPriceUpdate extends AbstractIndexerPlugin
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * Process row indexation into ES after the precedent stock index
     *
     * @param \Magento\Catalog\Model\Indexer\Product\Price $subject    The Price indexer
     * @param                                              $result
     * @param int[]                                        $productIds The product ids being reindexed
     *
     * @return void
     */
    public function afterExecute(
        \Magento\Catalog\Model\Indexer\Product\Price $subject,
        $result,
        $productIds
    ) {
        $this->processFullTextIndex($productIds);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * Process row indexation into ES after the precedent stock index
     *
     * @param \Magento\Catalog\Model\Indexer\Product\Price $subject   The Price indexer
     * @param                                              $result
     * @param int                                          $productId The product id being reindexed
     *
     * @return void
     */
    public function afterExecuteRow(
        \Magento\Catalog\Model\Indexer\Product\Price $subject,
        $result,
        $productId
    ) {
        $this->processFullTextIndex($productId);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * Process list indexation into ES after the precedent stock index
     *
     * @param \Magento\Catalog\Model\Indexer\Product\Price $subject    The Price indexer
     * @param                                              $result
     * @param int[]                                        $productIds The product ids being reindexed
     *
     * @return void
     */
    public function afterExecuteList(
        \Magento\Catalog\Model\Indexer\Product\Price $subject,
        $result,
        array $productIds
    ) {
        $this->processFullTextIndex($productIds);
    }
}
