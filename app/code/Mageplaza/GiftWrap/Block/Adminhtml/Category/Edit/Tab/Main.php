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

namespace Mageplaza\GiftWrap\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;
use Mageplaza\GiftWrap\Model\Category;
use Mageplaza\GiftWrap\Model\Config\Source\StatusFactory;

/**
 * Class Main
 * @package Mageplaza\GiftWrap\Block\Adminhtml\Category\Edit\Tab
 */
class Main extends Generic
{
    /**
     * Category instance
     *
     * @var Category
     */
    protected $_category;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * Main constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CollectionFactory $collectionFactory
     * @param StatusFactory $statusFactory
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CollectionFactory $collectionFactory,
        StatusFactory $statusFactory,
        Store $systemStore,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->statusFactory = $statusFactory;
        $this->systemStore = $systemStore;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $categoryObject = $this->getCategoryObject();

        /** @var Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('main_fieldset', ['legend' => __('General')]);

        if ($categoryObject->getId()) {
            $fieldset->addField('category_id', 'hidden', ['name' => 'category_id']);
        }

        $fieldset->addField('name', 'text', [
            'name' => 'name',
            'label' => __('Name'),
            'title' => __('Name'),
            'required' => true
        ]);

        $fieldset->addField('status', 'select', [
            'name' => 'status',
            'label' => __('Status'),
            'title' => __('Status'),
            'values' => $this->statusFactory->create()->toOptionArray()
        ]);

        $fieldset->addField('description', 'textarea', [
            'name' => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
        ]);

        /** @var RendererInterface $rendererBlock */
        $rendererBlock = $this->getLayout()->createBlock(Element::class);
        $fieldset->addField('store_id', 'multiselect', [
            'name' => 'store_id',
            'label' => __('Store View(s)'),
            'title' => __('Store View(s)'),
            'values' => $this->systemStore->getStoreValuesForForm(false, true),
        ])->setRenderer($rendererBlock)->setSize(5);

        $customers = $this->collectionFactory->create()->toOptionArray();
        array_unshift($customers, [
            'value' => '',
            'label' => __('--- Please Select ---')
        ]);
        $fieldset->addField('customer_group', 'multiselect', [
            'name' => 'customer_group',
            'label' => __('Customer Group(s)'),
            'title' => __('Customer Group(s)'),
            'values' => $customers,
        ])->setSize(5);

        $fieldset->addField('sort_order', 'text', [
            'name' => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'class' => 'validate-digits',
            'note' => __('Default is 0. The one with the smallest order will display first.')
        ]);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @inheritdoc
     */
    protected function _initFormValues()
    {
        $object = $this->getCategoryObject();

        if (!$object->getId() && $object->getData('store_id') === null) {
            $object->setData('store_id', 0);
        }

        if (!$object->getId() && $object->getData('customer_group') === null) {
            $group = array_keys($this->collectionFactory->create()->toOptionArray());
            $object->setData('customer_group', implode(',', $group));
        }

        $this->getForm()->addValues($object->getData());

        return parent::_initFormValues();
    }

    /**
     * Return category object
     *
     * @return Category
     */
    protected function getCategoryObject()
    {
        if ($this->_category === null) {
            return $this->_coreRegistry->registry('mpgiftwrap_category');
        }

        return $this->_category;
    }
}
