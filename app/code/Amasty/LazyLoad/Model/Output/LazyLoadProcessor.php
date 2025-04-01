<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Lazy Load for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\Output;

use Amasty\LazyLoad\Model\Asset\Collector\PreloadImageCollector;
use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\LazyLoad\Model\LazyScript\LazyScriptProvider;
use Amasty\LazyLoad\Model\OptionSource\PreloadStrategy;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfig;
use Amasty\LazyLoad\Model\Output\LazyConfig\LazyConfigFactory;
use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;

class LazyLoadProcessor implements OutputProcessorInterface
{
    private const IMG_REGEX_PATTERN = 'img';
    private const BACKGROUND_IMAGE_REGEX_PATTERN = 'background_image';
    private const PAGE_BUILDER_REGEX_PATTERN = 'page_builder';

    public const LAZY_LOAD_PLACEHOLDER = 'src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABC'
    . 'AQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII="';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var DataObjectFactory
     */
    private $lazyConfigFactory;

    /**
     * @var DataObject
     */
    private $lazyConfig;

    /**
     * @var LazyScriptProvider
     */
    private $lazyScriptProvider;

    /**
     * @var PreloadImageCollector
     */
    private $preloadImageCollector;

    /**
     * @var ReplacerCompositeInterface
     */
    private $imageReplacer;

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
        LazyScriptProvider $lazyScriptProvider,
        LazyConfigFactory $lazyConfigFactory,
        PreloadImageCollector $preloadImageCollector,
        ReplacerCompositeInterface $imageReplacer
    ) {
        $this->configProvider = $configProvider;
        $this->lazyScriptProvider = $lazyScriptProvider;
        $this->lazyConfigFactory = $lazyConfigFactory;
        $this->preloadImageCollector = $preloadImageCollector;
        $this->imageReplacer = $imageReplacer;
    }

    public function process(string &$output): bool
    {
        if ($this->configProvider->isEnabled() && $this->getLazyConfig()->getData(LazyConfig::IS_LAZY)) {
            $this->processLazyImages($output);

            if ($this->getLazyConfig()->hasData(LazyConfig::LAZY_SCRIPT)) {
                $this->addLazyScript($output, $this->getLazyConfig()->getData(LazyConfig::LAZY_SCRIPT));
            }
        }

        return true;
    }

    public function processLazyImages(&$output)
    {
        $tempOutput = preg_replace('/<script[^>]*>(?>.*?<\/script>)/is', '', $output);

        foreach ($this->regexPatterns as $patternKey => $regexConfig) {
            $this->currentRegexPattern = $patternKey;
            if (preg_match_all('/' . $this->getRegex($regexConfig) . '/is', $tempOutput, $images)) {
                $output = $this->modifyOutputByPattern($output, $images);
            }
        }
    }

    private function modifyOutputByPattern(string $output, array $images): string
    {
        $skipCounter = 1;
        $preloadStrategy = $this->getLazyConfig()->getData(LazyConfig::LAZY_PRELOAD_STRATEGY);
        $userAgentIgnoreList = $this->getLazyConfig()->getData(LazyConfig::USER_AGENT_IGNORE_LIST);

        foreach ($images[0] as $key => $image) {
            $newImg = $image;

            if ($skipCounter <= $this->getLazyConfig()->getData(LazyConfig::LAZY_SKIP_IMAGES)) {
                $imgPaths = $this->resolvePath($images, $key);
                foreach ($imgPaths as $imgPath) {
                    if ($this->getLazyConfig()->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT)) {
                        if (!$this->skipIfContain($image, $userAgentIgnoreList)) {
                            $algorithm = $this->regexPatterns[$this->currentRegexPattern]['algorithm']
                                ?? ReplacerCompositeInterface::REPLACE_BEST;
                            $newImg = $this->imageReplacer->replace(
                                $algorithm,
                                $image,
                                $imgPath
                            );
                            $output = str_replace($image, $newImg, $output);
                            $image = $newImg;

                            $newImgPath = $this->imageReplacer->replaceImagePath(
                                $algorithm,
                                $imgPath
                            );
                            $this->preloadImageCollector->addImageAsset($newImgPath);
                        }
                    } else {
                        if ($preloadStrategy == PreloadStrategy::SKIP_IMAGES) {
                            $this->preloadImageCollector->addImageAsset($imgPath);
                            $skipCounter++;
                            continue;
                        }

                        $algorithm = $this->regexPatterns[$this->currentRegexPattern]['algorithm']
                            ?? ReplacerCompositeInterface::REPLACE_PICTURE;
                        $newImg = $this->imageReplacer->replace(
                            $algorithm,
                            $image,
                            $imgPath
                        );
                        $output = str_replace($image, $newImg, $output);
                        $image = $newImg;
                    }
                }

                $skipCounter++;
                continue;
            }

            if (!$this->skipIfContain($image, $this->getLazyConfig()->getData(LazyConfig::LAZY_IGNORE_LIST))
                && $this->currentRegexPattern == self::IMG_REGEX_PATTERN
                && !$this->isThirdPartyAttribute($images, $key)
            ) {
                $regexpGroupByName = $this->regexPatterns[$this->currentRegexPattern]['group_by_name'];
                $replace = 'src="' . $images[$regexpGroupByName['src']][$key] . '"';
                $newImg = str_replace($replace, self::LAZY_LOAD_PLACEHOLDER . ' data-am' . $replace, $image);
            }

            if ($this->getLazyConfig()->getData(LazyConfig::IS_REPLACE_WITH_USER_AGENT)
                && !$this->skipIfContain($image, $userAgentIgnoreList)
            ) {
                $algorithm = $this->regexPatterns[$this->currentRegexPattern]['algorithm']
                    ?? ReplacerCompositeInterface::REPLACE_BEST;
                $imgPaths = $this->resolvePath($images, $key);
                foreach ($imgPaths as $imgPath) {
                    $newImg = $this->imageReplacer->replace(
                        $algorithm,
                        $newImg,
                        $imgPath
                    );
                }
            }

            $newImg = preg_replace('/srcset=[\"\'\s]+(.*?)[\"\']+/is', '', $newImg);
            $output = str_replace($image, $newImg, $output);
        }

        return $output;
    }

    public function addLazyScript(&$output, $lazyScriptType)
    {
        $lazy = '<script>window.amlazy = function() {'
            . 'if (typeof window.amlazycallback !== "undefined") {'
            . 'setTimeout(window.amlazycallback, 500);setTimeout(window.amlazycallback, 1500);}'
            . '}</script>';
        if ($lazyScript = $this->lazyScriptProvider->get($lazyScriptType)) {
            $lazy .= $lazyScript->getCode();
        }

        $output = str_ireplace('</body', $lazy . '</body', $output);
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

    public function setLazyConfig(DataObject $lazyConfig): self
    {
        $this->lazyConfig = $lazyConfig;

        return $this;
    }

    public function getLazyConfig(): DataObject
    {
        if ($this->lazyConfig === null) {
            $this->lazyConfig = $this->lazyConfigFactory->create();
        }

        return $this->lazyConfig;
    }

    private function getRegex(array $regexConfig): string
    {
        $regexTmpl = $regexConfig['pattern'];
        switch ($this->currentRegexPattern) {
            case self::IMG_REGEX_PATTERN:
                $imgAttributes = $this->getLazyConfig()
                    ->getData(LazyConfig::IMG_OPTIMIZER_SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES);
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

    /**
     * Only for Img regex
     *
     * @param array $images
     * @param int $key
     * @return bool
     */
    private function isThirdPartyAttribute(array $images, int $key): bool
    {
        $result = false;
        $imgAttributes = $this->getLazyConfig()
            ->getData(LazyConfig::IMG_OPTIMIZER_SUPPORT_THIRD_PARTY_IMAGE_ATTRIBUTES);
        if ($imgAttributes) {
            $index = null;
            $regexpGroupByName = $this->regexPatterns[$this->currentRegexPattern]['group_by_name'];
            foreach ($regexpGroupByName as $groupName => $groupNumber) {
                if ($groupName != 'src' && !empty($images[$groupName][$key])) {
                    $index = $groupNumber;
                    break;
                }
            }
            $result = $index && !empty($images[$index][$key]);
        }

        return $result;
    }
}
