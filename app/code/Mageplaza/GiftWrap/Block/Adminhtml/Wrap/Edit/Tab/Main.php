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

namespace Mageplaza\GiftWrap\Block\Adminhtml\Wrap\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\Store;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\PriceFactory;
use Mageplaza\GiftWrap\Model\Config\Source\StatusFactory;
use Mageplaza\GiftWrap\Model\ResourceModel\Category\CollectionFactory;
use Mageplaza\GiftWrap\Model\Wrap;
use Mageplaza\GiftWrap\Model\Config\Source\WrapType;

/**
 * Class Main
 * @package Mageplaza\GiftWrap\Block\Adminhtml\Wrap\Edit\Tab
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Main extends Generic
{
    /**
     * Wrap instance
     *
     * @var Wrap
     */
    protected $_wrap;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StatusFactory
     */
    protected $statusFactory;

    /**
     * @var PriceFactory
     */
    protected $priceFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Main constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param CollectionFactory $collectionFactory
     * @param StatusFactory $statusFactory
     * @param PriceFactory $priceFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        CollectionFactory $collectionFactory,
        StatusFactory $statusFactory,
        PriceFactory $priceFactory,
        Data $helper,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->statusFactory     = $statusFactory;
        $this->priceFactory      = $priceFactory;
        $this->helper            = $helper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $wrapObject = $this->getWrapObject();

        /** @var Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset('main_fieldset', ['legend' => __('General')]);

        $fieldset->addType('price', Price::class);

        if ($wrapObject->getId()) {
            $fieldset->addField('wrap_id', 'hidden', ['name' => 'wrap_id']);
        }

        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'required' => true
        ]);

        $fieldset->addField('status', 'select', [
            'name'   => 'status',
            'label'  => __('Status'),
            'title'  => __('Status'),
            'values' => $this->statusFactory->create()->toOptionArray()
        ]);

        $fieldset->addField('price_type', 'select', [
            'name'   => 'price_type',
            'label'  => __('Price'),
            'title'  => __('Price'),
            'values' => $this->priceFactory->create()->toOptionArray()
        ]);

        /** @var Store $store */
        $store  = $this->_storeManager->getStore();
        $symbol = $store->getBaseCurrency()->getCurrencySymbol();
        $fieldset->addField('amount', 'price', [
            'name'  => 'amount',
            'label' => __('Amount'),
            'title' => __('Amount'),
            'class' => 'validate-number validate-zero-or-greater',
        ])->setAfterElementHtml($symbol);

        $fieldset->addField('description', 'textarea', [
            'name'  => 'description',
            'label' => __('Description'),
            'title' => __('Description'),
        ]);

        $fieldset->addField('image', 'image', [
            'name'  => 'image',
            'label' => __('Image'),
            'title' => __('Image')
        ]);

        if ($wrapObject->getWrapType() === WrapType::GIFT_WRAP_WRAP_TYPE) {
            $fieldset->addField('qty_limit', 'text', [
                'name'  => 'qty_limit',
                'label' => __('Quantity Limit'),
                'title' => __('Quantity Limit'),
                'class' => 'validate-digits',
                'note'  => __('Item number can be in a gift wrap. If empty or zero, no limitation.')
            ]);
        }

        $fieldset->addField('wrap_type', 'hidden', [
            'name'  => 'wrap_type',
            'label' => __('Wrap Type'),
            'title' => __('Wrap Type'),
            'value' => $wrapObject->getWrapType()
        ]);

        $fieldset->addField('category', 'multiselect', [
            'name'   => 'category',
            'label'  => __('Category'),
            'title'  => __('Category'),
            'values' => $this->collectionFactory->create()->addFieldToFilter('status', 1)->toOptionArray(),
        ])->setSize(5);

        $fieldset->addField('sort_order', 'text', [
            'name'  => 'sort_order',
            'label' => __('Sort Order'),
            'title' => __('Sort Order'),
            'class' => 'validate-digits',
            'note'  => __('Default is 0. The one with the smallest order will display first.')
        ]);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @inheritdoc
     */
    protected function _initFormValues()
    {
        $object = $this->getWrapObject();

        $this->getForm()->addValues($object->getData());

        return parent::_initFormValues();
    }

    /**
     * Return wrap object
     *
     * @return Wrap
     */
    protected function getWrapObject()
    {
        if ($this->_wrap === null) {
            return $this->_coreRegistry->registry('mpgiftwrap_wrap');
        }

        return $this->_wrap;
    }
}
