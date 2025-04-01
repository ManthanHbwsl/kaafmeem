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

namespace Smile\ElasticsuiteCatalog\Plugin\Indexer\Category\Save;

use Smile\ElasticsuiteCatalog\Plugin\Indexer\AbstractIndexerPlugin;

/**
 * Plugin that proceed products reindex after category reindexing
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCatalog
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ReindexProductsAfterSave extends AbstractIndexerPlugin
{
    /**
     * Reindex category's products after reindexing the category
     *
     * @param \Magento\Catalog\Model\Category $subject The cateogry being reindexed
     *
     * @return void
     */
    public function afterReindex(
        \Magento\Catalog\Model\Category $subject
    ) {
        if (!empty($subject->getAffectedProductIds())) {
            $this->processFullTextIndex($subject->getAffectedProductIds());
        }
    }
}
