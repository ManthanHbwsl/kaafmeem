<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Page Speed Tools for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * TODO Refactor
 */
class Resolutions implements OptionSourceInterface
{
    public const MOBILE = 1;
    public const TABLET = 2;

    public const RESOLUTIONS = [
        self::TABLET => [
            'dir' => 'amasty' . DIRECTORY_SEPARATOR .  'amopttablet' . DIRECTORY_SEPARATOR,
            'path' => 'tablet_path',
            'width' => 768,
            'min-width' => 480
        ],
        self::MOBILE => [
            'dir' => 'amasty' . DIRECTORY_SEPARATOR .  'amoptmobile' . DIRECTORY_SEPARATOR,
            'path' => 'mobile_path',
            'width' => 480
        ]
    ];

    public const WEBP_DIR = 'amasty' . DIRECTORY_SEPARATOR .  'webp' . DIRECTORY_SEPARATOR;

    use ToOptionArrayTrait;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::MOBILE => __('Mobile'),
            self::TABLET => __('Tablet'),
        ];
    }
}
