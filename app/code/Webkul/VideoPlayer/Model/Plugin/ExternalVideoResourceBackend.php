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
namespace Webkul\VideoPlayer\Model\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\Gallery;
use Magento\Framework\DB\Select;

/**
 * Media Resource decorator
 */
class ExternalVideoResourceBackend
{
    /**
     * Helper variable
     *
     * @var [Webkul\VideoPlayer\Helper\Data]
     */
    private $helper;
    /**
     * @param \Webkul\VideoPlayer\Helper\Data $helper
     */
    public function __construct(
        \Webkul\VideoPlayer\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Plugin for after create batch base select action
     *
     * @param Gallery $originalResourceModel
     * @param Select $select
     * @return Select
     */
    public function afterCreateBatchBaseSelect(Gallery $originalResourceModel, Select $select)
    {
        if ($this->helper->getModuleConfig('general_settings/active')) {
            $select = $select->columns([
                'video_source' => 'default_value_video.source'
            ]);
        }
        return $select;
    }
}
