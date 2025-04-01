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
 * Define VideoSource class
 */
class VideoSource implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Upload Variable
     */
    public const UPLOAD = 'upload';

    /**
     * VideoURL Variable
     */
    public const VIDEOURL = 'url';

    /**
     * ToOptionArray function
     *
     * @return Array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::UPLOAD,
                'label' => __('Upload'),
            ],
            [
                'value' => self::VIDEOURL,
                'label' => __('Video Url'),
            ]
        ];
    }
}
