<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_FreeShippingBar
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FreeShippingBar\Model\Shippingbar;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Templates
 * @package Mageplaza\FreeShippingBar\Model\Shippingbar
 */
class Templates implements ArrayInterface
{
    const TEMPLATEDEFAULT = 0;
    const TEMPLATE_1 = 1;
    const TEMPLATE_2 = 2;
    const TEMPLATE_3 = 3;
    const TEMPLATE_4 = 4;
    const TEMPLATE_5 = 5;
    const TEMPLATE_6 = 6;
    /** path folder contain images background*/
    const BACKGROUND_IMAGES_RELATIVE_PATH = 'mageplaza/freeshippingbar/images/background';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::TEMPLATEDEFAULT,
                'label' => __('Template Default')
            ],
            [
                'value' => self::TEMPLATE_1,
                'label' => __('Template 1')
            ],
            [
                'value' => self::TEMPLATE_2,
                'label' => __('Template 2')
            ],
            [
                'value' => self::TEMPLATE_3,
                'label' => __('Template 3')
            ],
            [
                'value' => self::TEMPLATE_4,
                'label' => __('Template 4')
            ],
            [
                'value' => self::TEMPLATE_5,
                'label' => __('Template 5')
            ],
            [
                'value' => self::TEMPLATE_6,
                'label' => __('Template 6')
            ],
        ];

        return $options;
    }

    /**
     * @return array
     */
    public function getTemplateModelArray()
    {
        $templates = [
            self::TEMPLATEDEFAULT => [
                'bar_opacity' => 1,
                'bar_background' => '#0099e5',
                'bar_text_color' => '#FFFFFF',
                'bar_link_color' => '#F5FF0F',
                'goal_text_color' => '#F5FF0F',
                'font_text' => 'Roboto',
                'font_size' => '14',
                'bg_url_image' => ''
            ],
            self::TEMPLATE_1 => [
                'bar_opacity' => 1,
                'bar_background' => '#9C980C',
                'bar_text_color' => '#FFFFFF',
                'bar_link_color' => '#F5FF0F',
                'goal_text_color' => '#F5FF0F',
                'font_text' => 'Poppins',
                'font_size' => '15',
                'bg_url_image' => '/s/t/star.png'
            ],
            self::TEMPLATE_2 => [
                'bar_opacity' => 1,
                'bar_background' => '#e8562a',
                'bar_text_color' => '#FFFFFF',
                'bar_link_color' => '#FFFFFF',
                'goal_text_color' => '#FFF03D',
                'font_text' => 'Roboto',
                'font_size' => '14',
                'bg_url_image' => ''
            ],
            self::TEMPLATE_3 => [
                'bar_opacity' => 1,
                'bar_background' => '#B323C2',
                'bar_text_color' => '#FFFFFF',
                'bar_link_color' => '#FFFFFF',
                'goal_text_color' => '#F5FF0F',
                'font_text' => 'Inconsolata',
                'font_size' => '17',
                'bg_url_image' => '/s/h/sharp.png'
            ],
            self::TEMPLATE_4 => [
                'bar_opacity' => 1,
                'bar_background' => '#0C020D',
                'bar_text_color' => '#2139FF',
                'bar_link_color' => '#FF2E79',
                'goal_text_color' => '#FF3BB6',
                'font_text' => 'Oswald',
                'font_size' => '19',
                'bg_url_image' => '/v/a/valentine2.png'
            ],
            self::TEMPLATE_5 => [
                'bar_opacity' => 1,
                'bar_background' => '#8C3F22',
                'bar_text_color' => '#FFFFFF',
                'bar_link_color' => '#FFFFFF',
                'goal_text_color' => '#F5FF0F',
                'font_text' => 'Arimo',
                'font_size' => '20',
                'bg_url_image' => '/g/i/gift-box.png'
            ],
            self::TEMPLATE_6 => [
                'bar_opacity' => 1,
                'bar_background' => '#1EC20E',
                'bar_text_color' => '#F5FF0F',
                'bar_link_color' => '#F5FF0F',
                'goal_text_color' => '#FFFFFF',
                'font_text' => 'Ubuntu',
                'font_size' => '16',
                'bg_url_image' => '/c/a/cart.png'
            ]
        ];

        return $templates;
    }
}
