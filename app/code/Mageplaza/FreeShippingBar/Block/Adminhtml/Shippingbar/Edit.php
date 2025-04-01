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

namespace Mageplaza\FreeShippingBar\Block\Adminhtml\Shippingbar;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Mageplaza\FreeShippingBar\Model\Shippingbar;

/**
 * Class Edit
 * @package Mageplaza\FreeShippingBar\Block\Adminhtml\Shippingbar
 */
class Edit extends Container
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Shipping Bar edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mageplaza_FreeShippingBar';
        $this->_controller = 'adminhtml_shippingbar';
        parent::_construct();
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
    }

    /**
     * Retrieve text for header element depending on loaded Shippingbar
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Shippingbar $shippingbar */
        $shippingbar = $this->_coreRegistry->registry('current_shippingbar');
        if ($shippingbar->getId()) {
            return __('Edit Free Shipping Bar "%1"', $this->escapeHtml($shippingbar->getName()));
        }

        return __('New Free Shipping Bar');
    }
}
