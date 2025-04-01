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
 * Define Customer Group Class
 */
class CustomerGroup implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Option variable
     *
     * @var [Magento\Store\Ui\Component\Listing\Column\Store\Options]
     */
    public $_options;

    /**
     * GroupCollection variable
     *
     * @var [Magento\Customer\Model\ResourceModel\Group\CollectionFactory]
     */
    public $_groupCollectionFactory;
    
    /**
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
     */
    public function __construct(
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
    ) {
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }
 
    /**
     * ToOptionArray function
     *
     * @return Array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupCollectionFactory->create()->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
