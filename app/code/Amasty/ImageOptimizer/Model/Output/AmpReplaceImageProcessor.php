<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Image Optimizer for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Output;

use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\ObjectManagerInterface;

class AmpReplaceImageProcessor extends ImageReplaceProcessor
{
    public const IMAGE_REGEXP = '<amp-img([^>]*?)src=(\"|\'|)(.*?)(\"|\'| )(.*?)>';

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    public function __construct(
        ConfigProvider $configProvider,
        DataObjectFactory $dataObjectFactory,
        ObjectManagerInterface $objectManager,
        ReplacerCompositeInterface $imageReplacer,
        ReplaceConfig\ReplaceConfigFactory $replaceConfigFactory
    ) {
        parent::__construct($configProvider, $dataObjectFactory, $objectManager, $imageReplacer, $replaceConfigFactory);
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Prevent is_lazy checking on AMP pages
     * @return DataObject
     */
    public function getLazyConfig(): DataObject
    {
        return $this->dataObjectFactory->create();
    }
}
