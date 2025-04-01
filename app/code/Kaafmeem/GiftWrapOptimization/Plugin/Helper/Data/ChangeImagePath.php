<?php
declare(strict_types=1);

namespace Kaafmeem\GiftWrapOptimization\Plugin\Helper\Data;

use Amasty\PageSpeedTools\Model\Image\ReplacerCompositeInterface;
use Mageplaza\GiftWrap\Helper\Data;

class ChangeImagePath
{
    /**
     * @var ReplacerCompositeInterface
     */
    private $imageReplacer;

    public function __construct(
        ReplacerCompositeInterface $imageReplacer
    ) {
        $this->imageReplacer = $imageReplacer;
    }

    /**
     * @param Data $subject
     * @param string $result
     * @return string
     */
    public function afterGetWraps(Data $subject, $result)
    {
        $resultArray = $subject->jsonDecodeData($result);

        foreach ($resultArray as &$wrap) {
            $image = $wrap['image'] ?? null;

            if ($image) {
                $wrap['image'] = $this->imageReplacer->replaceImagePath(
                    ReplacerCompositeInterface::REPLACE_BEST,
                    $image
                );
            }
        }

        return $subject->jsonEncodeData($resultArray);
    }
}
