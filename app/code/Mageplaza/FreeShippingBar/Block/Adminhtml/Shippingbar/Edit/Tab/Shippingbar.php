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

namespace Mageplaza\FreeShippingBar\Block\Adminhtml\Shippingbar\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Status;

/**
 * Class Shippingbar
 * @package Mageplaza\FreeShippingBar\Block\Adminhtml\Shippingbar\Edit\Tab
 */
class Shippingbar extends Generic implements TabInterface
{
    /**
     * @type Yesno
     */
    protected $_yesno;

    /**
     * @var Status|Status
     */
    protected $_status;

    /**
     * @type Config
     */
    protected $_wysiwygConfigModel;

    /**
     * @type Store
     */
    protected $_store;

    /**
     * @var Collection
     */
    protected $_groupCollection;

    /**
     * Shippingbar constructor.
     *
     * @param Yesno $yesno
     * @param Status $status
     * @param Config $wysiwygConfig
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $store
     * @param Collection $groupCollection
     * @param array $data
     */
    public function __construct(
        Yesno $yesno,
        Status $status,
        Config $wysiwygConfig,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $store,
        Collection $groupCollection,
        array $data = []
    ) {
        $this->_yesno = $yesno;
        $this->_status = $status;
        $this->_wysiwygConfigModel = $wysiwygConfig;
        $this->_store = $store;
        $this->_groupCollection = $groupCollection;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Generic
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\FreeShippingBar\Model\Shippingbar $shippingbar */
        $shippingbar = $this->_coreRegistry->registry('current_shippingbar');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('shippingbar_');
        $form->setFieldNameSuffix('shippingbar');

        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Shipping Bar Information'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true,
        ]);

        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'required' => true,
            'values' => $this->_status->toOptionArray()
        ]);

        $fieldset->addField('priority', 'text', [
            'name' => 'priority',
            'label' => __('Priority'),
            'title' => __('Priority'),
            'required' => false,
        ]);

        /** @var RendererInterface $rendererBlock */
        $rendererBlock = $this->getLayout()->createBlock(Element::class);
        $fieldset->addField('store_id', 'multiselect', [
            'name' => 'store_id',
            'label' => __('Store Views'),
            'title' => __('Store Views'),
            'required' => true,
            'values' => $this->_store->getStoreValuesForForm(false, true)
        ])->setRenderer($rendererBlock);

        $fieldset->addField('customer_group_id', 'multiselect', [
            'name' => 'customer_group_id',
            'label' => __('Customer Groups'),
            'title' => __('Customer Groups'),
            'required' => true,
            'values' => $this->_groupCollection->toOptionArray()
        ]);

        $fieldset->addField('from_date', 'date', [
            'name' => 'from_date',
            'label' => __('From Date'),
            'title' => __('From Date'),
            'date_format' => $this->_localeDate->getDateFormatWithLongYear(),
            'class' => 'validate-date',
        ]);

        $fieldset->addField('to_date', 'date', [
            'name' => 'to_date',
            'label' => __('To Date'),
            'title' => __('To Date'),
            'date_format' => $this->_localeDate->getDateFormatWithLongYear(),
            'class' => 'validate-date',
        ]);

        $form->addValues($shippingbar->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('General');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
