<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_VideoPlayer
 * @author    Webkul
 * @copyright Copyright (c)   Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\VideoPlayer\Block\Plugin\Product\Media;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Gallery for catalog product gallery handlers plugins.
 */
class Gallery
{
    /**
     * @var Json
     */
    private $json;

    /**
     * @param Json $json
     */
    public function __construct(
        Json $json
    ) {
        $this->json = $json;
    }

    /**
     * AfterGetOption function
     *
     * @param \Magento\Catalog\Block\Product\View\Gallery $subject
     * @param [Magento\Framework\Serialize\Serializer\Json] $result
     * @return json
     */
    public function afterGetOptionsMediaGalleryDataJson(
        \Magento\Catalog\Block\Product\View\Gallery $subject,
        $result
    ) {
        $result = $this->json->unserialize($result);
        $parentProduct = $subject->getProduct();
        if ($parentProduct->getTypeId() == Configurable::TYPE_CODE) {
            /** @var Configurable $productType */
            $productType = $parentProduct->getTypeInstance();
            $products = $productType->getUsedProducts($parentProduct);
            /** @var Product $product */
            foreach ($products as $product) {
                $key = $product->getId();
                $result[$key] = $this->getProductGalleryOverride($product);
            }
        }
        return $this->json->serialize($result);
    }

    /**
     * Get Provide Gallery Override function
     *
     * @param [Magento\Catalog\Model\Product] $product
     * @return Array
     */
    private function getProductGalleryOverride($product)
    {
        $result = [];
        $images = $product->getMediaGalleryImages();
        foreach ($images as $image) {
            $result[] = [
                'mediaType' => $image->getMediaType(),
                'videoUrl' => $image->getVideoUrl(),
                'isBase' => $product->getImage() == $image->getFile(),
                'position' => (int)$image->getPosition(),
            ];
        }
        usort($result, function ($item1, $item2) {
            return $item1['position'] <=> $item2['position'];
        });
        return $result;
    }
}
