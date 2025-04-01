<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Block\Adminhtml\City\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Custom\City\Model\System\Config\Status;
use Custom\City\Service\GetActiveLocaleService;
use Magento\Directory\Model\Config\Source\Country;
use Custom\City\Model\StateFactory;

class Info extends Generic implements TabInterface
{
    /** @var Config */
    protected $_wysiwygConfig;

    /** @var Status */
    protected $cityStatus;

    /** @var GetActiveLocaleService */
    private $getActiveLocaleService;

    /** @var Country */
    private $countryFactory;

    /** @var StateFactory */
    private $stateFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Status $cityStatus,
        Country $countryFactory,
        StateFactory $stateFactory,
        GetActiveLocaleService $getActiveLocaleService,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->cityStatus = $cityStatus;
        $this->countryFactory = $countryFactory;
        $this->stateFactory = $stateFactory;
        $this->getActiveLocaleService = $getActiveLocaleService;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $locales = $this->getActiveLocaleService->get();
        /** @var $model Custom\City\Model\City */
        $model = $this->_coreRegistry->registry('city_city');
        $data = $model->getData();
        if (count($data) == 0) {
            $data = $this->_session->getFormData();
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('');
        $form->setFieldNameSuffix('city');

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
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'required' => true
            ]
        );
        if (count($locales) > 0) {
            foreach ($locales as $locale) {
                $value = isset($data[$locale]) && !empty($data[$locale]) ? $data[$locale] : '';
                $localeField = $fieldset->addField(
                    'city-' . $locale,
                    'text',
                    [
                        'name' => 'city_name[' . $locale . ']',
                        'label' => __('City [' . $locale . ']'),
                        'required' => false,
                        'note' => 'Add translation for city in ' . $locale
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
                          $('#city-'+'" . $locale . "').val('" . $data[$locale] . "');
                    }
                );
                </script>"
                    );
                }
            }
        }
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'options' => $this->cityStatus->toOptionArray()
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
        $params = $this->getRequest()->getParams();
        $_states_options = array();
        $_states_options[''] = __('Please Select');
        if (isset($params['id']) && (isset($data['country_id']) && !empty($data['country_id']))) {
            $_states = $this->stateFactory->create()->getCollection();
            $_states = $_states->addFieldToFilter('country_id', $data['country_id']);
            if (count($_states->getData()) > 0) {
                foreach ($_states as $_state) {
                    $_states_options[$_state['region_id']] = $_state['default_name'];
                }
            }
        }

        $required = false;
        $fieldset->addField(
            'state_id',
            'select',
            [
                'name' => 'state_id',
                'label' => __('State'),
                'values' => $_states_options,
                'required' => $required,
                'note' => 'State is optional now, if you have state then select state.'
            ]
        );
        /*
           * Add Ajax to the Country select box html output
           */
        $country->setAfterElementHtml(
            "
            <script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate) {
                   $('#edit_form').on('change', '#country_id', function(event){
                        $.ajax({
                               url : '" . $this->getUrl('city/city/regionlist') . "country/' +  $('#country_id').val(),
                                type: 'get',
                                dataType: 'json',
                               showLoader:true,
                               success: function(data){
                                    $('#state_id').empty();
                                    $('#state_id').append(data.htmlconent);
                               }
                            });
                   })
                }

            );
            </script>"
        );
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
        return __('City Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('City Info');
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
