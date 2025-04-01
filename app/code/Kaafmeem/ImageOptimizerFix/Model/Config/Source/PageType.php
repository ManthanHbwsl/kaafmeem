<?php
declare(strict_types=1);

namespace Kaafmeem\ImageOptimizerFix\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PageType implements OptionSourceInterface
{
    public const PAGE_TYPE_HOME_PAGE = 'cms_index_index';
    public const PAGE_TYPE_CMS_PAGE = 'cms_page_view';

    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Home Page'),
                'value' => self::PAGE_TYPE_HOME_PAGE
            ],
            [
                'label' => __('CMS Page'),
                'value' => self::PAGE_TYPE_CMS_PAGE
            ]
        ];

        return $options;
    }
}
