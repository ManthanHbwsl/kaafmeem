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

namespace Webkul\VideoPlayer\Model\Config\Source;

/**
 * Define PlayerOptions class
 */
class PlayerOptions implements \Magento\Framework\Option\ArrayInterface
{

    public const PLAYLARGE = 'play-large';
    public const MUTE = 'mute';
    public const VOLUME = 'volume';
    public const CAPTIONS = 'captions';
    public const FULLSCREEN = 'fullscreen';

    /**
     * ToOptionArray function
     *
     * @return Array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PLAYLARGE,
                'label' => __('Play Large'),
            ],
            [
                'value' => self::MUTE,
                'label' => __('Mute'),
            ],
            [
                'value' => self::VOLUME,
                'label' => __('Volume'),
            ],
            [
                'value' => self::FULLSCREEN,
                'label' => __('Fullscreen'),
            ],
        ];
    }
}
