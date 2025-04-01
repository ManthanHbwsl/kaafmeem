<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */

namespace Custom\City\Block\Adminhtml\State\Import\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Directory\Model\Config\Source\Country;
use Custom\City\Service\GetActiveLocaleService;

class Info extends Generic implements TabInterface
{
    /** @var Country */
    private $countryFactory;

    /** @var GetActiveLocaleService */
    private $getActiveLocaleService;

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
        $data = [];
        $data = $this->_session->getFormData();
        $this->_session->setFormData(null);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('');
        $form->setFieldNameSuffix('import_state');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Upload CSV File For') . __('States')]
        );
        $fieldset->addField(
            'country_id',
            'select',
            [
                'name' => 'country_id',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $this->countryFactory->toOptionArray(),
                'required' => true,
                'class' => 'validate-select'
            ]
        );


        $additional = $fieldset->addField(
            'file',
            'file',
            [
                'name' => 'csv',
                'label' => __('File'),
                'title' => __('Upload CSV'),
                'required' => true,
                'class' => 'required-file validate-fileextensions',
            ]
        );
        $additional->setAfterElementHtml(
            "<script type=\"text/javascript\">
			require([
				'jquery',
				'jquery/ui',
				'jquery/validate',
				'mage/translate'
			], function ($) {
				 $.validator.addMethod(
					'validate-fileextensions', function (v, elm) {

						var extensions = ['csv'];
						if (!v) {
							return true;
						}
						with (elm) {
							var ext = value.substring(value.lastIndexOf('.') + 1);
							for (i = 0; i < extensions.length; i++) {
								if (ext == extensions[i]) {
									return true;
								}
							}
						}
						return false;
					}, $.mage.__('Disallowed file type, only csv extension is allowed.'));
			});
			</script>
		"
        );
        $fieldset->addField(
            'link',
            'link',
            [
                'name' => 'sample_download_link',
                'label' => __('Download Sample'),
                'link_label' => __('Download Sample'),
                'title' => __('Download Sample CSV'),
                'value' => __('Download Sample'),
                'href' => $this->getUrl('city/city/download/', ['file' => 'state']),
                'note' => '<br />' . __('States') . __(
                        'import file will be imported in csv format and contains two columns mandatory and other columns are for translation in other languages like below:'
                    ) . '<br> <table border="1" width="100%"><tr><th>State</th><th>State Code</th><th>ar_SA</th><th>fr_FR</th></tr><tr><td align="center">12345</td><td align="center">Abcd</td><td align="center">اسم الولاية</td><td align="center">Nom d\'état</td></tr></table>'
            ]
        );
        $data['link'] = __('Download Sample CSV');
        $locales = $this->getActiveLocaleService->get();
        if (!empty($locales)) {
            $fieldset->addField(
                'link_id',
                'note',
                [
                    'name' => 'active_locales',
                    'label' => __('Active Locales'),
                    'text' => implode(', ', $locales),
                ]
            );
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
        return __('States Import Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('States Import Info');
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
 