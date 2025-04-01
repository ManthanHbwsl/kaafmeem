<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-banner
 * @version   1.1.10
 * @copyright Copyright (C) 2023 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);


namespace Mirasvit\Banner\Block\Placeholder;


class SliderRenderer extends AbstractRenderer
{
    protected $_template = 'Mirasvit_Banner::placeholder/sliderRenderer.phtml';

    public function getBanners()
    {
        return $this->bannerService->getApplicableBanners($this->getPlaceholder(), 100);
    }

    public function getSliderConfig(): array
    {
        $config = [];

        $additional = $this->getPlaceholder()->getAdditional();

        if (!$additional) {
            return $config;
        }

        $config = [
            'lazy'          => true,
            'slidesPerView' => 1,
            'loop'          => is_null($additional['loop']) ? false : (bool)$additional['loop']
        ];

        if ($this->isDisplayNavigation()) {
            $config['navigation'] = [
                'nextEl' => ".swiper-button-next",
                'prevEl' => ".swiper-button-prev"
            ];
        }

        if ($this->isDisplayPagination()) {
            $config['pagination'] = [
                'el'        => '.swiper-pagination',
                'clickable' => true
            ];
        }

        if (isset($additional['autoplay']) && $additional['autoplay']) {
            $config['autoplay'] = [
                'delay'                => isset($additional['timeout']) && is_numeric($additional['timeout'])
                    ? (int)$additional['timeout'] * 1000
                    : 3000,
                'disableOnInteraction' => false
            ];
        }

        return $config;
    }

    public function isDisplayNavigation(): bool
    {
        $bannersCount = count($this->getBanners());

        if ($bannersCount <= 1) {
            return false;
        }

        return (bool)$this->getConfigByKey('navigation');
    }

    public function isDisplayPagination(): bool
    {
        $bannersCount = count($this->getBanners());

        if ($bannersCount <= 1) {
            return false;
        }

        return (bool)$this->getConfigByKey('pagination');
    }

    public function getButton(string $type): string
    {
        $bannersCount = count($this->getBanners());

        if ($bannersCount <= 1) {
            return '';
        }

        return (string)$this->getConfigByKey($type);
    }

    private function getConfigByKey(string $key): ?string
    {
        $additional = $this->getPlaceholder()->getAdditional();

        if (!$additional) {
            return null;
        }

        return isset($additional[$key]) ? (string)$additional[$key] : null;
    }
}
