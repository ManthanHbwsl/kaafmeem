<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile ElasticSuite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteVirtualCategory
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2020 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\ElasticsuiteVirtualCategory\Plugin\Catalog\Category;

use Magento\Catalog\Model\ResourceModel\Category\Flat as FlatResource;
use Magento\Framework\DataObject;

/**
 * Plugin to ensure proper loading of virtual rules when using flat categories.
 *
 * @category Smile
 * @package  Smile\ElasticsuiteVirtualCategory
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class FlatPlugin
{
    /**
     * @var \Smile\ElasticsuiteVirtualCategory\Model\Category\Attribute\VirtualRule\ReadHandler
     */
    private $readHandler;

    /**
     * FlatPlugin constructor.
     *
     * @param \Smile\ElasticsuiteVirtualCategory\Model\Category\Attribute\VirtualRule\ReadHandler $readHandler Read Handler
     */
    public function __construct(
        \Smile\ElasticsuiteVirtualCategory\Model\Category\Attribute\VirtualRule\ReadHandler $readHandler
    ) {
        $this->readHandler = $readHandler;
    }

    /**
     * Process correct loading of virtual rule when loading a category via the Flat Resource
     * Built around load() method because the afterLoad() Resource method is only triggered
     * when using the EntityManager, and is not triggered when calling load() method on model explicitely.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param FlatResource $categoryResource Flat Category Resource
     * @param FlatResource $result
     * @param DataObject   $category         The category being loaded
     */
    public function afterLoad(FlatResource $categoryResource, $result, $category)
    {
        if ($category->getVirtualRule() == null || is_string($category->getVirtualRule())) {
            $this->readHandler->execute($category);
        }

        return $result;
    }
}
