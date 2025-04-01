<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Image Optimizer for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Output;

use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\ImageOptimizer\Model\Output\ReplaceConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig;
use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\ObjectManagerInterface;

class ImageReplaceProcessor implements OutputProcessorInterface
{
    private const IMG_REGEX_PATTERN = 'img';
    private const BACKGROUND_IMAGE_REGEX_PATTERN = 'background_image';
    private const PAGE_BUILDER_REGEX_PATTERN = 'page_builder';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ReplacerCompositeInterface
     */
    private $imageReplacer;

    /**
     * @var ReplaceConfig\ReplaceConfigFactory
     */
    private $replaceConfigFactory;

    /**
     * @var LazyConfig\LazyConfig|DataObject
     */
    private $lazyConfig;

    /**
     * @var ReplaceConfig\ReplaceConfig
     */
    private $replaceConfig;

    /**
     * @var string
     */
    private $currentRegexPattern;

    /**
     * @var array[]
     */
    private $regexPatterns = [
        self::IMG_REGEX_PATTERN => [
            'pattern' => '<img\s*(?:%img_attributes%|(?<any>[a-z\-_]+\s*\=\s*[\'\"](.*?)[\'\"].*?\s*))+.*?>',
            'group_by_name' => [],
            'algorithm' => null,
            'is_replace_all_found_attributes' => false
        ],
        self::BACKGROUND_IMAGE_REGEX_PATTERN => [
            'pattern' => 'background\-image\s*:\s*url\s*\(\s*[\'\" ]?(?<background_image>.*?)[\'\" ]?\s*\)',
            'group_by_name' => ['background_image' => 1],
            'algorithm' => ReplacerCompositeInterface::REPLACE_BEST,
            'is_replace_all_found_attributes' => true
        ],
        self::PAGE_BUILDER_REGEX_PATTERN => [
            'pattern' => '(?:data-background-images\s*\=\s*[\'\"]{'
                . '(?:\\\\[\'\"]desktop_image\\\\[\'\"]\s*\:\s*\\\\[\'\"](?<desktop_image>.*?)\\\\[\'\"]\s?,?\s?'
                . '|\\\\[\'\"]mobile_image\\\\[\'\"]\s*\:\s*\\\\[\'\"](?<mobile_image>.*?)\\\\[\'\"]\s?,?\s?'
                . '|\\\\[\'\"][a-z_\-]+\\\\[\'\"]\s*\:\s*\\\\[\'\"].*?\\\\[\'\"]\s?,?\s?)+}'
                . '[\'\"].*?\s*)',
            'group_by_name' => ['desktop_image' => 1, 'mobile_image' => 2],
            'algorithm' => ReplacerCompositeInterface::REPLACE_BEST,
            'is_replace_all_found_attributes' => true
        ]
    ];

    public function __construct(
        ConfigProvider $configProvider,
        DataObjectFactory $dataObjectFactory,
        ObjectManagerInterface $objectManager,
        ReplacerCompositeInterface $imageReplacer,
        ReplaceConfig\ReplaceConfigFactory $replaceConfigFactory
    ) {
        $this->configProvider = $configProvider;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->objectManager = $objectManager;
        $this->imageReplacer = $imageReplacer;
        $this->replaceConfigFactory = $replaceConfigFactory;
    }

    public function process(string &$output): bool
    {
        if ($this->getLazyConfig() !== null && $this->getLazyConfig()->getData('is_lazy')) {
            return true;
        }

        if ($this->getReplaceConfig()->getData(ReplaceConfig\ReplaceConfig::IS_REPLACE_IMAGES)) {
            $tempOutput = preg_replace('/<script.*?>.*?<\/script.*?>/is', '', $output);

            foreach ($this->regexPatterns as $patternKey => $regexConfig) {
                $this->currentRegexPattern = $patternKey;
                if (preg_match_all('/' . $this->getRegex($regexConfig) . '/is', $tempOutput, $images)) {
                    $output = $this->modifyOutputByPattern($output, $images);
                }
            }
        }

        return true;
    }

    private function modifyOutputByPattern(string $output, array $images): string
    {
        foreach ($images[0] as $key => $image) {
            if ($this->skipIfContain(
                $image,
                $this->getReplaceConfig()->getData(ReplaceConfig\ReplaceConfig::REPLACE_IMAGES_IGNORE_LIST)
            )) {
                continue;
            }

            if ($this->getReplaceConfig()->getData(ReplaceConfig\ReplaceConfig::IS_REPLACE_WITH_USER_AGENT)) {
                $algorithm = ReplacerCompositeInterface::REPLACE_BEST;
            } else {
                $algorithm = ReplacerCompositeInterface::REPLACE_PICTURE;
            }
            $algorithm = $this->regexPatterns[$this->currentRegexPattern]['algorithm'] ?? $algorithm;
            $imgPaths = $this->resolvePath($images, $key);
            foreach ($imgPaths as $imgPath) {
                $newImg = $this->imageReplacer->replace(
                    $algorithm,
                    $image,
                    $imgPath
                );
                $output = str_replace($image, $newImg, $output);
                $image = $newImg;
            }
        }

        return $output;
    }

    private function skipIfContain(string $searchString, array $list): bool
    {
        $skip = false;
        foreach ($list as $item) {
            if (strpos($searchString, $item) !== false) {
                $skip = true;
                break;
            }
        }

        return $skip;
    }

    public function getLazyConfig(): DataObject
    {
        if ($this->lazyConfig === null) {
            try {
                $this->lazyConfig = $this->objectManager->get(LazyConfig\LazyConfig::class);
            } catch (\Throwable $e) {
                $this->lazyConfig = $this->dataObjectFactory->create();
            }
        }

        return $this->lazyConfig;
    }

    public function getReplaceConfig(): DataObject
    {
        if ($this->replaceConfig === null) {
            $this->replaceConfig = $this->replaceConfigFactory->create();
        }

        return $this->replaceConfig;
    }

    private function getRegex(array $regexConfig): string
    {
        $regexTmpl = $regexConfig['pattern'];
        switch ($this->currentRegexPattern) {
            case self::IMG_REGEX_PATTERN:
                $imgAttributes = $this->getReplaceConfig()
                    ->getData(ReplaceConfig\ReplaceConfig::SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES);
                // the src group will be last each time
                $imgAttributes[] = 'src';
                $imgAttributesRegexp = '';
                $groupNumber = 0;
                foreach ($imgAttributes as $index => $imgAttribute) {
                    $groupName = str_replace('-', '_', $imgAttribute);
                    $groupNumber += 2;
                    $imgAttributesRegexp .= ($index ? '|' : '')
                        . '(?<' . $groupName . '>'
                        . $imgAttribute . '\s*\=\s*[\'\"](.*?)[\'\"].*?\s*)';

                    $this->regexPatterns[self::IMG_REGEX_PATTERN]['group_by_name'][$groupName] = $groupNumber;
                }
                $regex = str_replace('%img_attributes%', $imgAttributesRegexp, $regexTmpl);
                break;
            default:
                $regex = $regexTmpl;
        }

        return $regex;
    }

    private function resolvePath(array $images, int $key): array
    {
        $regexConfig = $this->regexPatterns[$this->currentRegexPattern];
        $isReplaceAllFoundAttributes = $regexConfig['is_replace_all_found_attributes'];
        $regexpGroupByName = $regexConfig['group_by_name'];
        $result = [];
        foreach ($regexpGroupByName as $groupName => $groupNumber) {
            if (!empty($images[$groupName][$key])) {
                $result[] = $images[$groupNumber][$key];
                if (!$isReplaceAllFoundAttributes) {
                    break;
                }
            }
        }

        return $result;
    }
}
