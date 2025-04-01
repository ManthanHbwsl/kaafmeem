<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Page Speed Tools for Magento 2 (System)
*/

declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\Image;

class ReplacerComposite implements ReplacerCompositeInterface
{
    /**
     * @var ReplacerInterface[]
     */
    private $imageReplacers;

    /**
     * @var array
     */
    private $imagePathCache = [];

    public function __construct(array $imageReplacers = [])
    {
        foreach ($imageReplacers as $replacer) {
            if (!($replacer instanceof ReplacerInterface)) {
                throw new \LogicException(
                    sprintf('Image replacer must implement %s', ReplacerInterface::class)
                );
            }
        }

        $this->imageReplacers = $imageReplacers;
    }

    public function replace(string $algorithm, string $image, string $imagePath): string
    {
        $this->checkAlgorithm($algorithm);
        $imagePath = $this->prepareImagePath($imagePath);

        return $this->imageReplacers[$algorithm]->execute($image, $imagePath);
    }

    public function replaceImagePath(string $algorithm, string $imagePath): string
    {
        if (!isset($this->imagePathCache[$imagePath])) {
            $this->checkAlgorithm($algorithm);
            $imagePath = $this->prepareImagePath($imagePath);
            $this->imagePathCache[$imagePath] = $this->imageReplacers[$algorithm]->getReplaceImagePath($imagePath);
        }

        return $this->imagePathCache[$imagePath];
    }

    private function checkAlgorithm($algorithm)
    {
        if (!isset($this->imageReplacers[$algorithm])) {
            throw new \LogicException("Image replacer for algorithm '{$algorithm}' is not defined.");
        }
    }

    private function prepareImagePath($imagePath)
    {
        return (string)strtok($imagePath, '?'); //remove get-parameters if they exists
    }
}
