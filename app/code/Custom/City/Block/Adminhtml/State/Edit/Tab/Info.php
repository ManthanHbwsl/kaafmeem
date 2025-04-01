<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Block\Adminhtml\State\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Custom\City\Service\GetActiveLocaleService;
use Magento\Directory\Model\Config\Source\Country;

class Info extends Generic implements TabInterface
{
    /** @var  Config */
    protected $wysiwygConfig;

    /** @var GetActiveLocaleService */
    private $getActiveLocaleService;

    /** @var Country */
    private $countryFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Country $countryFactory,
        GetActiveLocaleService $getActiveLocaleService,
        array $data = []
    ) {
        $this->countryFactory = $countryFactory;
        $this->getActiveLocaleService = $getActiveLocaleService;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $locales = $this->getActiveLocaleService->get();
        /** @var $model Custom\City\Model\City */
        $model = $this->_coreRegistry->registry('city_state');
        $data = $model->getData();
        if (count($data) == 0) {
            $data = $this->_session->getFormData();
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('');
        $form->setFieldNameSuffix('state');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }
        $fieldset->addField(
            'default_name',
            'text',
            [
                'name' => 'default_name',
                'label' => __('State Name'),
                'required' => true
            ]
        );
        if (count($locales) > 0) {
            foreach ($locales as $locale) {
                $value = isset($data[$locale]) && !empty($data[$locale]) ? $data[$locale] : '';
                $localeField = $fieldset->addField(
                    'state-' . $locale,
                    'text',
                    [
                        'name' => 'state_name[' . $locale . ']',
                        'label' => __('State Name [' . $locale . ']'),
                        'required' => false,
                        'note' => 'Add translation for state name in ' . $locale
                    ]
                );
                if (isset($data[$locale])) {
                    $localeField->setAfterElementHtml(
                        "
                    <script type=\"text/javascript\">
                        require([
                        'jquery',
                        'mage/template',
                        'jquery/ui',
                        'mage/translate'
                    ],
                    function($, mageTemplate) {
                          $('#state-'+'" . $locale . "').val('" . $data[$locale] . "');
                    }
                );
                </script>"
                    );
                }
            }
        }
        $fieldset->addField(
            'code',
            'text',
            [
                'name' => 'code',
                'label' => __('State Code'),
                'required' => true
            ]
        );
        $country = $fieldset->addField(
            'country_id',
            'select',
            [
                'name' => 'country_id',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $this->countryFactory->toOptionArray(),
                'required' => true
            ]
        );
        if (!empty($data) && count($data) > 0 && isset($data['region_id'])) {
            $data['id'] = $data['region_id'];
        }
        $form->setValues($data);
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
        return __('State Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('State Info');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
 