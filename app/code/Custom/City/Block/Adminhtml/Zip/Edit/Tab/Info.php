<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Block\Adminhtml\Zip\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Custom\City\Model\System\Config\Status;
use Magento\Directory\Model\Config\Source\Country;
use Custom\City\Model\StateFactory;
use Custom\City\Model\CityFactory;
use Custom\City\System\Config\Config as CityConfig;

class Info extends Generic implements TabInterface
{
    /** @var Config */
    protected $_wysiwygConfig;

    /** @var Status */
    protected $_zipStatus;

    /** @var Country */
    protected $_countryFactory;

    /** @var StateFactory */
    protected $_stateFactory;

    /** @var CityFactory */
    protected $_cityFactory;

    /** @var CityConfig  */
    protected $cityConfig;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        Status $zipStatus,
        Country $countryFactory,
        StateFactory $stateFactory,
        CityFactory $cityFactory,
        CityConfig $cityConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_zipStatus = $zipStatus;
        $this->_countryFactory = $countryFactory;
        $this->_stateFactory = $stateFactory;
        $this->_cityFactory = $cityFactory;
        $this->cityConfig = $cityConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model Custom\City\Model\Zip */
        $model = $this->_coreRegistry->registry('city_zip');
        $data = $model->getData();
        if (count($data) == 0) {
            $data = $this->_session->getFormData();
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('');
        $form->setFieldNameSuffix('zip');

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
            'zip_name',
            'text',
            [
                'name' => 'zip_name',
                'label' => __('Zip Code'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'options' => $this->_zipStatus->toOptionArray()
            ]
        );
        $country = $fieldset->addField(
            'country_id',
            'select',
            [
                'name' => 'country_id',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $this->_countryFactory->toOptionArray(),
                'required' => true
            ]
        );
        $params = $this->getRequest()->getParams();
        $_states_options = array();
        $_states_options[''] = __('Please Select');
        $_cities_options = [];
        $_cities_options[''] = __('Please Select');
        if (isset($params['id']) && (isset($data['country_id']) && !empty($data['country_id']))) {
            $_states = $this->_stateFactory->create()->getCollection();
            $_states = $_states->addFieldToFilter('country_id', $data['country_id']);
            if (count($_states->getData()) > 0) {
                foreach ($_states as $_state) {
                    $_states_options[$_state['region_id']] = $_state['default_name'];
                }
            }
            if (!$this->cityConfig->isStateAvailable()) {
                $_cities = $this->_cityFactory->create()->getCollection();
                $_cities = $_cities->addFieldToFilter('country_id', $data['country_id']);
                if (count($_cities->getData()) > 0) {
                    foreach ($_cities as $_city) {
                        $_cities_options[$_city['id']] = $_city['city'];
                    }
                }
            }
        }

        if (isset($params['id']) && (isset($data['state_id']) && !empty($data['state_id']))) {
            $_cities_options = [];
            $_cities_options[''] = __('Please Select');
            $_cities = $this->_cityFactory->create()->getCollection();
            $_cities = $_cities->addFieldToFilter('state_id', $data['state_id']);
            if (count($_cities->getData()) > 0) {
                foreach ($_cities as $_city) {
                    $_cities_options[$_city['id']] = $_city['city'];
                }
            }
        }


        $required = false;
        $state = $fieldset->addField(
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
                                    if(data.counter==0){
                                        $.ajax({
                                               url : '" . $this->getUrl('city/zip/citylist') . "country/' +  $('#country_id').val(),
                                               type: 'get',
                                               dataType: 'json',
                                               showLoader:true,
                                               success: function(data){
                                                  $('#city_id').empty();
                                                  $('#city_id').append(data.htmlconent);
                                               }
                                        });
                                    }
                               }
                            })
                   });
                    $('#edit_form').on('click', '#load-cities', function(event){
                         if($('#country_id').val()==''){
                            alert('Please select country first!');
                         }else{
                             $.ajax({
                                      url : '" . $this->getUrl('city/zip/citylist') . "country/' +  $('#country_id').val(),
                                      type: 'get',
                                      dataType: 'json',
                                      showLoader:true,
                                      success: function(data){
                                             $('#city_id').empty();
                                             $('#city_id').append(data.htmlconent);
                                       }
                             });
                         }
                   });
                }
            );
            </script>"
        );
        $city = $fieldset->addField(
            'city_id',
            'select',
            [
                'name' => 'city_id',
                'label' => __('City'),
                'values' => $_cities_options,
                'required' => true,
                'note' => 'Click on "Load Cities" button to load cities without state.'
            ]
        );
        $city->setAfterElementHtml("<button class='button' type='button' id='load-cities'>Load Cities</button>");
        /*
           * Add Ajax to the Country select box html output
           */
        $state->setAfterElementHtml(
            "
            <script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate) {
                   $('#edit_form').on('change', '#state_id', function(event){
                        $.ajax({
                               url : '" . $this->getUrl('city/zip/citylist') . "state/' +  $('#state_id').val(),
                                type: 'get',
                                dataType: 'json',
                               showLoader:true,
                               success: function(data){
                                    $('#city_id').empty();
                                    $('#city_id').append(data.htmlconent);
                               }
                            });
                   });
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
