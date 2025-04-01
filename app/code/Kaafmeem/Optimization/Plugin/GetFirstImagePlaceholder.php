<?php
declare(strict_types=1);

namespace Kaafmeem\Optimization\Plugin;

use MGS\Fbuilder\Block\Widget\OwlCarousel;
use MGS\Fbuilder\Helper\Data;

class GetFirstImagePlaceholder
{
    public const REGXEX = '/<img.*?src=["|\'](.*?)["|\']/';
    /**
     * @var Data
     */
    private $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param OwlCarousel $subject
     * @return array
     */
    public function beforeToHtml(OwlCarousel $subject): array
    {
        $rawHtml = $subject->getHtmlSlider();
        $html = $this->helper->decodeHtmlTag($rawHtml);
        $imageUrl = $this->getFirstImageUrl($html);

        if ($imageUrl) {
            $subject->setData('first_image', $imageUrl);
        }

        return [];
    }

    /**
     * @param string $sourceHtml
     * @return string|null
     */
    private function getFirstImageUrl(string $sourceHtml):? string
    {
        $url = null;

        if (preg_match(self::REGXEX, $sourceHtml, $matches)) {
            $url = $matches[1] ?? null;
        }

        return $url;
    }
}
