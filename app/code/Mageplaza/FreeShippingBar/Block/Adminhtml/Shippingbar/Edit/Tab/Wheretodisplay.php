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

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Mageplaza\FreeShippingBar\Block\Adminhtml\Render\Field\Codehtml;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Allowshow;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Position;

/**
 * Class Wheretodisplay
 * @package Mageplaza\FreeShippingBar\Block\Adminhtml\Shippingbar\Edit\Tab
 */
class Wheretodisplay extends Generic implements TabInterface
{
    /**
     * @type Yesno
     */
    protected $_yesno;

    /**
     * @var Position
     */
    protected $_position;

    /**
     * @var Allowshow
     */
    protected $_allowshow;

    /**
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var FieldFactory
     */
    protected $fieldFactory;

    /**
     * Wheretodisplay constructor.
     *
     * @param Yesno $yesno
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Position $position
     * @param Allowshow $allowshow
     * @param Fieldset $rendererFieldset
     * @param FieldFactory $fieldFactory
     * @param array $data
     */
    public function __construct(
        Yesno $yesno,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Position $position,
        Allowshow $allowshow,
        Fieldset $rendererFieldset,
        FieldFactory $fieldFactory,
        array $data = []
    ) {
        $this->_yesno = $yesno;
        $this->_position = $position;
        $this->_allowshow = $allowshow;
        $this->_rendererFieldset = $rendererFieldset;
        $this->fieldFactory = $fieldFactory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get current shippingbar
     * @return mixed
     */
    public function getCurrentShippingbar()
    {
        return $this->_coreRegistry->registry('current_shippingbar');
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
            'legend' => __('Where to display'),
            'class' => 'fieldset-wide'
        ]);

        $position = $fieldset->addField('position', 'select', [
            'name' => 'position',
            'label' => __('Position'),
            'title' => __('Position'),
            'required' => true,
            'values' => $this->_position->toOptionArray(),
        ]);

        $allowShow = $fieldset->addField('allowshow', 'select', [
            'name' => 'allowshow',
            'label' => __('Allow Show'),
            'title' => __('Allow Show'),
            'required' => true,
            'note' => __('Select page(s) to display the free shipping bar'),
            'values' => $this->_allowshow->toOptionArray(),
        ], 'include_pages');

        $includePage = $fieldset->addField('include_pages', 'textarea', [
            'name' => 'include_pages',
            'label' => __('Which page(s) to show'),
            'title' => __('Which page(s) to show'),
            'note' => __('Enter handle name(s) of page(s) to display free shipping bars.'
                . '<br>Page(s) are separated by line breaks.'),
        ]);

        $includeUrl = $fieldset->addField('include_pages_url_contain', 'textarea', [
            'name' => 'include_pages_url_contain',
            'label' => __('Include page(s) with URL(s) containing'),
            'title' => __('Include page(s) with URL(s) containing'),
            'note' => __(
                'Page(s) with URL(s) containing the above path(s) will be selected to display free shipping bars.'
                . '<br>Page(s) are separated by line breaks.'
            ),
        ]);

        $excludePage = $fieldset->addField('exclude_pages', 'textarea', [
            'name' => 'exclude_pages',
            'label' => __('Exclude pages'),
            'title' => __('Exclude pages'),
            'note' => __('Enter handle name(s) of page(s) NOT to display free shipping bars.'
                . '<br>Page(s) are separated by line breaks.'),
        ]);

        $excludeUrl = $fieldset->addField('exclude_pages_url_contain', 'textarea', [
            'name' => 'exclude_pages_url_contain',
            'label' => __('Exclude Page with URL contains'),
            'title' => __('Exclude Page with URL contains'),
            'note' => __(
                'Page(s) with URL(s) containing the above path(s) will not selected to display free shipping bars.'
                . '<br>Page(s) are separated by line breaks.'
            ),
        ]);

        $snippet = $fieldset->addField('snippet_code_insert', Codehtml::class, [
            'name' => 'snippet_code_insert',
            'subject' => $this,
        ]);

        $refField = $this->fieldFactory->create(
            [
                'fieldData' =>
                    [
                        'value' => implode(',', [
                            Position::BOTTOM_FIXED_BAR,
                            Position::TOP_CONTENT,
                            Position::TOP_FIXED_BAR,
                            Position::TOP_PAGE
                        ]),
                        'separator' => ','
                    ],
                'fieldPrefix' => ''
            ]
        );

        $this->setChild('form_after', $this->getLayout()->createBlock(Dependence::class)
            ->addFieldMap($position->getHtmlId(), $position->getName())
            ->addFieldMap($allowShow->getHtmlId(), $allowShow->getName())
            ->addFieldMap($includePage->getHtmlId(), $includePage->getName())
            ->addFieldMap($includeUrl->getHtmlId(), $includeUrl->getName())
            ->addFieldMap($excludePage->getHtmlId(), $excludePage->getName())
            ->addFieldMap($excludeUrl->getHtmlId(), $excludeUrl->getName())
            ->addFieldMap($snippet->getHtmlId(), $snippet->getName())
            ->addFieldDependence($allowShow->getName(), $position->getName(), $refField)
            ->addFieldDependence($includePage->getName(), $position->getName(), $refField)
            ->addFieldDependence($includeUrl->getName(), $position->getName(), $refField)
            ->addFieldDependence($excludePage->getName(), $position->getName(), $refField)
            ->addFieldDependence($excludeUrl->getName(), $position->getName(), $refField)
            ->addFieldDependence($includePage->getName(), $allowShow->getName(), 'specific_pages')
            ->addFieldDependence($includeUrl->getName(), $allowShow->getName(), 'specific_pages')
            ->addFieldDependence($snippet->getName(), $position->getName(), 'insert_snippet'));

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
        return __('Where to display');
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
