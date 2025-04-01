<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_VideoPlayer
 * @author    Webkul
 * @copyright Copyright (c)   Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\VideoPlayer\Block\Adminhtml\Product\Edit;

use Magento\Framework\Data\Form\Element\Fieldset;
use Webkul\VideoPlayer\Model\Config\Source\VideoSource;

/**
 * Class Video
 */
class NewVideo extends \Magento\ProductVideo\Block\Adminhtml\Product\Edit\NewVideo
{
    /**
     * videoSource variable
     *
     * @var [Webkul\VideoPlayer\Model\Config\Source\VideoSource]
     */
    protected $videoSource;

    /**
     * Helper variable
     *
     * @var [Webkul\VideoPlayer\Helper\Data]
     */
    protected $helper;

    /**
     * Construction function
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\ProductVideo\Helper\Media $mediaHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Webkul\VideoPlayer\Helper\Data $helper
     * @param VideoSource $videoSource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\ProductVideo\Helper\Media $mediaHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Webkul\VideoPlayer\Helper\Data $helper,
        VideoSource $videoSource,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $mediaHelper, $jsonEncoder, $data);
        $this->videoSource = $videoSource;
        $this->helper = $helper;
    }

    /**
     * Form preparation
     *
     * @return void
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'new_video_form',
                'class' => 'admin__scope-old',
                'enctype' => 'multipart/form-data',
            ]
        ]);
        $form->setUseContainer($this->getUseContainer());
        $form->addField('new_video_messages', 'note', []);
        $fieldset = $form->addFieldset('new_video_form_fieldset', []);
        $fieldset->addField(
            '',
            'hidden',
            [
                'name' => 'form_key',
                'value' => $this->getFormKey(),
            ]
        );
        $fieldset->addField(
            'item_id',
            'hidden',
            []
        );
        $fieldset->addField(
            'file_name',
            'hidden',
            []
        );
        $fieldset->addField(
            'video_provider',
            'hidden',
            [
                'name' => 'video_provider',
            ]
        );
        if ($this->helper->getModuleConfig('general_settings/active')) {

            $action = $fieldset->addField(
                'video_source',
                'select',
                [
                    'name' => 'video_source',
                    'label' => __('Video Source'),
                    'title' => __('Video Source'),
                    'required' => true,
                    'values' => $this->videoSource->toOptionArray(),
                    'class' => 'edited-data'
                ]
            );
            
            $fileUploadField = $fieldset->addField(
                'video_file',
                'file',
                [
                    'label' => __('Upload Video'),
                    'title' => __('Upload Video'),
                    'name' => 'video_file',
                    'class' => 'edited-data',
                    'after_element_html' => '<p style="margin-top:8px;">(Allowed Extension : mp4 and WebM)</p>'
                ]
            );
        }
        $videoUrl = $fieldset->addField(
            'video_url',
            'text',
            [
                'class' => 'edited-data validate-url',
                'label' => __('Url'),
                'title' => __('Url'),
                'required' => true,
                'name' => 'video_url',
                'note' => $this->getNoteVideoUrl(),
            ]
        );
        
        $fieldset->addField(
            'upload-video-url',
            'hidden',
            [
                'name' => 'upload-video-url',
                'id' => 'upload-video-url'
            ]
        );

        $fieldset->addField(
            'video_title',
            'text',
            [
                'class' => 'edited-data',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'name' => 'video_title',
            ]
        );
        $fieldset->addField(
            'video_description',
            'textarea',
            [
                'class' => 'edited-data',
                'label' => __('Description'),
                'title' => __('Description'),
                'name' => 'video_description',
            ]
        );
        $fieldset->addField(
            'new_video_screenshot',
            'file',
            [
                'label' => __('Preview Image'),
                'title' => __('Preview Image'),
                'name' => 'image',
                'required' => true
            ]
        );
        $fieldset->addField(
            'new_video_screenshot_preview',
            'button',
            [
                'class' => 'preview-image-hidden-input',
                'label' => '',
                'name' => '_preview',
            ]
        );
        $fieldset->addField(
            'new_video_get',
            'button',
            [
                'label' => '',
                'title' => 'Get Video Information',
                'name' => 'new_video_get',
                'value' => __('Get Video Information'),
                'class' => 'action-default'
            ]
        );
        $this->addMediaRoleAttributes($fieldset);
        $fieldset->addField(
            'new_video_disabled',
            'checkbox',
            [
                'class' => 'edited-data',
                'label' => __('Hide from Product Page'),
                'title' => __('Hide from Product Page'),
                'name' => 'disabled',
            ]
        );

        $this->setForm($form);
    }

    /**
     * Get widget options
     *
     * @return string
     */
    public function getWidgetOptions()
    {
        return $this->jsonEncoder->encode(
            [
                'saveVideoUrl' => $this->getUrl('catalog/product_gallery/upload'),
                'saveRemoteVideoUrl' => $this->getUrl('product_video/product_gallery/retrieveImage'),
                'saveVideoFileUrl' => $this->getUrl('videoplayer/gallery/upload'),
                'htmlId' => $this->getHtmlId(),
                'youTubeApiKey' => $this->mediaHelper->getYouTubeApiKey(),
                'videoSelector' => $this->videoSelector,
                'formKey' => $this->getFormKey()
            ]
        );
    }
}
