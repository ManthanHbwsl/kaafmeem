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
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\Framework\Registry;
use Magento\Framework\View\Asset\Repository;
use Mageplaza\FreeShippingBar\Helper\Image;
use Mageplaza\FreeShippingBar\Model\Shippingbar\FontText;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Templates;

/**
 * Class Whattodisplay
 * @package Mageplaza\FreeShippingBar\Block\Adminhtml\Shippingbar\Edit\Tab
 */
class Whattodisplay extends Generic implements TabInterface
{
    const OPACITY = 1;
    const BACKGROUND = '#0099e5';
    const TEXT_COLOR = '#FFFFFF';
    const LINK_COLOR = '#F5FF0F';
    const GOAL_TEXT_COLOR = '#F5FF0F';
    const IMAGE_BACKGROUND = '';
    const FONT_FAMILY = 'Roboto';
    const FONT_SIZE = '14';

    /**
     * @type Yesno
     */
    protected $_yesno;

    /**
     * @var Fieldset
     */
    protected $_rendererFieldset;

    /**
     * @var FontText
     */
    protected $_fontText;

    /**
     * @var Templates
     */
    protected $_templates;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var JsonData
     */
    protected $jsonHelper;

    /**
     * Asset service
     *
     * @var Repository
     */
    protected $_assetRepo;

    /**
     * Whattodisplay constructor.
     *
     * @param Yesno $yesno
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Fieldset $rendererFieldset
     * @param FontText $fontText
     * @param Templates $templates
     * @param Image $imageHelper
     * @param JsonData $jsonHelper
     * @param array $data
     */
    public function __construct(
        Yesno $yesno,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Fieldset $rendererFieldset,
        FontText $fontText,
        Templates $templates,
        Image $imageHelper,
        JsonData $jsonHelper,
        array $data = []
    ) {
        $this->_assetRepo = $context->getAssetRepository();
        $this->_yesno = $yesno;
        $this->_rendererFieldset = $rendererFieldset;
        $this->_fontText = $fontText;
        $this->_templates = $templates;
        $this->imageHelper = $imageHelper;
        $this->jsonHelper = $jsonHelper;

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
            'legend' => __('What to display'),
            'class' => 'fieldset-wide'
        ]);

        $fieldset->addField('el_message', 'note', [
            'name' => 'el_message'
        ]);

        $fieldset->addField('goal', 'text', [
            'name' => 'goal',
            'label' => __('Goal'),
            'title' => __('Goal'),
            'note' => __('Enter the free shipping threshold. '
                . 'Buyers whose orders reach this amount will receive delivery for free'),
            'required' => true,
            'class' => 'validate-zero-or-greater'
        ]);

        $idItemEditing = $this->getRequest()->getParam('rule_id');
        if ($idItemEditing) {
            $fieldset->addField('first_message', 'text', [
                'name' => 'first_message',
                'label' => __('The first message'),
                'title' => __('The first message'),
                'class' => 'validate-no-html-tags',
                'required' => true,
                'note' => __('Enter the initial message in the free shipping bar displayed to buyers'),
            ]);

            $fieldset->addField('below_goal_message', 'text', [
                'name' => 'below_goal_message',
                'label' => __('Below-goal message'),
                'title' => __('Below-goal message'),
                'class' => 'validate-no-html-tags',
                'required' => true,
                'note' => __('Enter the second message when buyers\' orders haven\'t reached the goal'),
            ]);

            $fieldset->addField('achieve_message', 'text', [
                'name' => 'achieve_message',
                'label' => __('Achieve-goal message'),
                'title' => __('Achieve-goal message'),
                'class' => 'validate-no-html-tags',
                'required' => true,
                'note' => __('Enter the congratulation message when buyers\' orders reach the goal'),
            ]);
        } else {
            $fieldset->addField('first_message', 'text', [
                'name' => 'first_message',
                'label' => __('The first message'),
                'title' => __('The first message'),
                'class' => 'validate-no-html-tags',
                'required' => true,
                'note' => __('Enter the initial message in the free shipping bar displayed to buyers'),
                'value' => __('Free Shipping for order over {{goal}}'),

            ]);

            $fieldset->addField('below_goal_message', 'text', [
                'name' => 'below_goal_message',
                'label' => __('Below-goal message'),
                'title' => __('Below-goal message'),
                'class' => 'validate-no-html-tags',
                'required' => true,
                'note' => __('Enter the second message when buyers\' orders haven\'t reached the goal'),
                'value' => __('Only {{below_goal}} away for free shipping'),
            ]);

            $fieldset->addField('achieve_message', 'text', [
                'name' => 'achieve_message',
                'label' => __('Achieve-goal message'),
                'title' => __('Achieve-goal message'),
                'class' => 'validate-no-html-tags',
                'required' => true,
                'note' => __('Enter the congratulation message when buyers\' orders reach the goal'),
                'value' => __('Congrats! You have got free shipping'),
            ]);
        }

        $fieldset->addField('click_able', 'select', [
            'name' => 'click_able',
            'label' => __('Clickable'),
            'title' => __('Clickable'),
            'values' => $this->_yesno->toOptionArray(),
            'note' => __('If Yes, the bar can be clicked to link to a different URL'),
        ]);

        $fieldset->addField('url_shippingbar', 'text', [
            'name' => 'url_shippingbar',
            'label' => __('Link URL'),
            'title' => __('Link URL'),
            'class' => 'validate-url',
            'required' => true,
            'note' => __('This can be the link to the Free Shipping policy page'),
        ]);

        $fieldset->addField('open_new_page', 'select', [
            'name' => 'open_new_page',
            'label' => __('Open a new page'),
            'title' => __('Open a new page'),
            'values' => $this->_yesno->toOptionArray(),
            'note' => __('Select Yes to open the link in a new tab'),
        ]);

        $fieldset = $form->addFieldset('design_template_fieldset', [
            'legend' => __('Design Template'),
            'class' => 'fieldset-wide'
        ]);

        if ($idItemEditing) {
            /** case edit data*/
            $fieldset->addField('template', 'select', [
                'name' => 'template',
                'label' => __('Load Template'),
                'title' => __('Load Template'),
                'required' => true,
                'values' => $this->_templates->toOptionArray(),
            ]);

            $fieldset->addField('bar_opacity', 'text', [
                'name' => 'bar_opacity',
                'label' => __('Bar Background Opacity'),
                'title' => __('Bar Background Opacity'),
                'note' => __('Limit value from 0 -> 1. If value higher than 1, system will use default value is 1'),
                'class' => 'validate-zero-or-greater'
            ]);

            $fieldset->addField('bar_background', 'text', [
                'name' => 'bar_background',
                'label' => __('Bar Background Color'),
                'title' => __('Bar Background Color'),
                'class' => 'jscolor {hash:true,refine:false}',
                'required' => true,
            ]);

            $fieldset->addField('bar_text_color', 'text', [
                'name' => 'bar_text_color',
                'label' => __('Bar Text Color'),
                'title' => __('Bar Text Color'),
                'class' => 'jscolor {hash:true,refine:false}',
                'required' => true,
            ]);

            $fieldset->addField('bar_link_color', 'text', [
                'name' => 'bar_link_color',
                'label' => __('Bar Link Color'),
                'title' => __('Bar Link Color'),
                'class' => 'jscolor {hash:true,refine:false}',
                'required' => true,
            ]);

            $fieldset->addField('goal_text_color', 'text', [
                'name' => 'goal_text_color',
                'label' => __('Goal text color'),
                'title' => __('Goal text color'),
                'class' => 'jscolor {hash:true,refine:false}',
                'required' => true,
            ]);

            $fieldset->addField('image', \Mageplaza\Core\Block\Adminhtml\Renderer\Image::class, [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'note' => __('Recommended size: 150x50 px. Supported format: jpg, jpeg, png.'),
                'path' => $this->imageHelper->getBaseMediaPath(Image::TEMPLATE_MEDIA_TYPE_BACKGROUND)
            ]);

            $fieldset->addField('font_text', 'select', [
                'name' => 'font_text',
                'label' => __('Fonts'),
                'title' => __('Fonts'),
                'required' => true,
                'values' => $this->_fontText->toOptionArray(),
            ]);

            $fieldset->addField('font_size', 'text', [
                'name' => 'font_size',
                'label' => __('Font Size'),
                'title' => __('Font Size'),
                'required' => true,
            ]);
        } else {
            /** case add new records*/
            $fieldset->addField('template', 'select', [
                'name' => 'template',
                'label' => __('Load Template'),
                'title' => __('Load Template'),
                'required' => true,
                'note' => __('Select a template for the free shipping bar'),
                'values' => $this->_templates->toOptionArray(),
            ]);

            $fieldset->addField('bar_opacity', 'text', [
                'name' => 'bar_opacity',
                'label' => __('Bar Background Opacity'),
                'title' => __('Bar Background Opacity'),
                'value' => self::OPACITY,
                'note' => __('Limit value from 0 -> 1. If value higher than 1, system will use default value is 1'),
                'class' => 'validate-zero-or-greater'
            ]);

            $fieldset->addField('bar_background', 'text', [
                'name' => 'bar_background',
                'label' => __('Bar Background Color'),
                'title' => __('Bar Background Color'),
                'class' => 'jscolor {hash:true,refine:false}',
                'required' => true,
                'note' => __('Select the color for the free shipping bar\'s background'),
                'value' => self::BACKGROUND,
            ]);

            $fieldset->addField('bar_text_color', 'text', [
                'name' => 'bar_text_color',
                'label' => __('Bar Text Color'),
                'title' => __('Bar Text Color'),
                'class' => 'jscolor {hash:true,refine:false}',
                'required' => true,
                'note' => __('Select the text\'s color'),
                'value' => self::TEXT_COLOR,
            ]);

            $fieldset->addField('bar_link_color', 'text', [
                'name' => 'bar_link_color',
                'label' => __('Bar Link Color'),
                'title' => __('Bar Link Color'),
                'class' => 'jscolor {hash:true,refine:false}',
                'required' => true,
                'note' => __('Select the link\'s color'),
                'value' => self::LINK_COLOR,
            ]);

            $fieldset->addField('goal_text_color', 'text', [
                'name' => 'goal_text_color',
                'label' => __('Goal text color'),
                'title' => __('Goal text color'),
                'class' => 'jscolor {hash:true,refine:false}',
                'required' => true,
                'value' => self::GOAL_TEXT_COLOR,
            ]);

            $fieldset->addField('image', \Mageplaza\Core\Block\Adminhtml\Renderer\Image::class, [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'note' => __('Recommended size: 150x50 px. Supported formats: jpg, jpeg, png.'),
                'path' => $this->imageHelper->getBaseMediaPath(Image::TEMPLATE_MEDIA_TYPE_BACKGROUND),
                'value' => self::IMAGE_BACKGROUND,
            ]);

            $fieldset->addField('font_text', 'select', [
                'name' => 'font_text',
                'label' => __('Font'),
                'title' => __('Font'),
                'required' => true,
                'note' => __('Select the text\'s font'),
                'values' => $this->_fontText->toOptionArray(),
                'value' => self::FONT_FAMILY,
            ]);

            $fieldset->addField('font_size', 'text', [
                'name' => 'font_size',
                'label' => __('Font Size'),
                'title' => __('Font Size'),
                'required' => true,
                'value' => self::FONT_SIZE,
            ]);
        }

        $form->addFieldset('preview_fieldset', [
            'legend' => __('Preview Template'),
            'class' => 'fieldset-wide'
        ]);

        /** get templates model */
        $templatesModel = $this->jsonHelper->jsonEncode($this->_templates->getTemplateModelArray());
        $getImageMediaMageplazaUrl = $this->jsonHelper->jsonEncode($this->getImageMediaMageplazaUrl());
        $getImageFSBMageplazaUrl = $this->jsonHelper->jsonEncode($this->getImageFSBMageplazaUrl());

        $renderer = $this->_rendererFieldset->setData('templatesModel', $templatesModel)
            ->setImageHelper($getImageMediaMageplazaUrl)
            ->setImageFSBMageplazaUrl($getImageFSBMageplazaUrl)
            ->setTemplate(
                'Mageplaza_FreeShippingBar::preview.phtml'
            );

        $form->addFieldset('template_fieldset', [
            'legend' => __('Load Template'),
            'class' => 'fieldset-wide'
        ])->setRenderer($renderer);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(Dependence::class)
                ->addFieldMap('shippingbar_click_able', 'click_able')
                ->addFieldMap('shippingbar_url_shippingbar', 'url_shippingbar')
                ->addFieldDependence('url_shippingbar', 'click_able', '1')
                ->addFieldMap('shippingbar_open_new_page', 'open_new_page')
                ->addFieldDependence('open_new_page', 'click_able', '1')
        );

        $form->addValues($shippingbar->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * get image url in pub/media/mageplaza
     * @return string
     */
    public function getImageMediaMageplazaUrl()
    {
        $baseMediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => 'media']);
        $mageplazaImagePath = $this->imageHelper->getBaseMediaPath(Image::TEMPLATE_MEDIA_TYPE_BACKGROUND);

        return $baseMediaUrl . $mageplazaImagePath;
    }

    /**
     * get image url in view/base/web/mageplaza
     * @return string
     */
    public function getImageFSBMageplazaUrl()
    {
        $imageFSBUrl = $this->_assetRepo->getUrl('Mageplaza_FreeShippingBar::mageplaza/freeshippingbar/background');

        return $imageFSBUrl;
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('What to display');
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
