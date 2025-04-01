<?php
declare(strict_types=1);

namespace Kaafmeem\ImageOptimizerFix\Model\PageType;

use Magento\Framework\View\Layout;
use Kaafmeem\ImageOptimizerFix\Model\Config\Source\PageType as PageTypeSource;

class PageValidator
{
    /**
     * @var Layout
     */
    private $layout;

    /**
     * @var array
     */
    private $results = [];

    public function __construct(
        Layout $layout
    ) {
        $this->layout = $layout;
    }

    public function isValid(array $pageTypes): bool
    {
        $key = implode('-', $pageTypes);
        if (!isset($this->results[$key])) {
            $handles = $this->layout->getUpdate()->getHandles();
            if (in_array(PageTypeSource::PAGE_TYPE_HOME_PAGE, $handles)) {
                // There are 2 defined handlers on the home page: cms_index_index and cms_page_view.
                //  We leave only one handler for accurate page validation.
                $handles = array_diff($handles, [PageTypeSource::PAGE_TYPE_CMS_PAGE]);
            }

            $this->results[$key] = (bool)array_intersect($handles, $pageTypes);
        }

        return $this->results[$key];
    }
}
