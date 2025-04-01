<?php
declare(strict_types=1);

namespace MGS\ThemeSettings\Plugin\Catalog\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;

class CategoryPlugin
{
    /**
     * @param Category $subject
     * @param Collection $result
     * @return Collection
     */
    public function afterGetChildrenCategories(Category $subject, Collection $result): Collection
    {
        $result->addAttributeToSelect(
            'fbuilder_thumbnail'
        )->addAttributeToSelect(
            'fbuilder_icon'
        )->addAttributeToSelect(
            'fbuilder_font_class'
        )->addAttributeToSelect(
            'description'
        );

        return $result;
    }
}
