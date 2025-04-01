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
 * @package     Mageplaza_GiftWrap
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftWrap\Block\Adminhtml\Wrap;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\GiftWrap\Model\Wrap;

/**
 * Class Edit
 * @package Mageplaza\GiftWrap\Block\Adminhtml\Wrap
 */
class Edit extends Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Mageplaza_GiftWrap';

    /**
     * @var string
     */
    protected $_controller = 'adminhtml_wrap';

    /**
     * Core registry
     *
     * @var Registry
     */
    public $_coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * @return Wrap
     */
    protected function getWrap()
    {
        return $this->_coreRegistry->registry('mpgiftwrap_wrap');
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->add(
            'save_and_continue',
            [
                'label'          => __('Save and Continue Edit'),
                'class'          => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ]
        );
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getWrap()->getId()) {
            return __('Edit Gift Wrap "%1"', $this->getWrap()->getName());
        }

        return __('New Gift Wrap');
    }
}
