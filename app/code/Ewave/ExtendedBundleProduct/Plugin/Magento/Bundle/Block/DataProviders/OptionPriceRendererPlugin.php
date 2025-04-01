<?php
declare(strict_types=1);

namespace Ewave\ExtendedBundleProduct\Plugin\Magento\Bundle\Block\DataProviders;

use Magento\Bundle\Block\DataProviders\OptionPriceRenderer;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class OptionPriceRendererPlugin
{
    /**
     * @param OptionPriceRenderer $subject
     * @param callable $proceed
     * @param Product $selection
     * @param array $arguments
     * @return string
     */
    public function aroundRenderTierPrice(
        OptionPriceRenderer $subject,
        callable $proceed,
        Product $selection,
        array $arguments = []
    ): string {
        if ($selection->getTypeId() == Configurable::TYPE_CODE) {
            return '';
        }

        return $proceed($selection, $arguments);
    }
}
