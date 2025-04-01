<?php
declare(strict_types=1);

namespace MGS\Fbuilder\Plugin\MGS\Fbuilder\Model\Category;

use MGS\Fbuilder\Model\Category\DataProvider;
use MGS\Fbuilder\Helper\Category as CategoryHelper;

class DataProviderPlugin
{
    /**
     * @var CategoryHelper
     */
    private $categoryHelper;

    /**
     * @var array
     */
    private $loadedData;

    /**
     * @param CategoryHelper $categoryHelper
     */
    public function __construct(CategoryHelper $categoryHelper)
    {
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * @param DataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetData(DataProvider $subject, $result): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $this->loadedData = $result;
        $category = $subject->getCurrentCategory();
        if ($category) {
            $categoryData = $this->loadedData[$category->getId()];

            foreach ($this->categoryHelper->getAdditionalImageTypes() as $imageType) {
                if (isset($categoryData[$imageType])) {
                    unset($categoryData[$imageType]);
                    $categoryData[$imageType][0]['name'] = $category->getData($imageType);
                    $categoryData[$imageType][0]['url'] = $this->categoryHelper->getImageUrl($category->getData($imageType));
                }
            }

            $this->loadedData[$category->getId()] = $categoryData;
        }

        return $this->loadedData;
    }
}
