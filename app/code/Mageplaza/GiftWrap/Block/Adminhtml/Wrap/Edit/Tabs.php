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

namespace Mageplaza\GiftWrap\Block\Adminhtml\Wrap\Edit;

use Exception;
use Mageplaza\GiftWrap\Model\Config\Source\WrapType;

/**
 * Class Tabs
 * @package Mageplaza\GiftWrap\Block\Adminhtml\Wrap\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('wrap_tabs');
        $this->setDestElementId('edit_form');
        $title = $this->getRequest()->getParam('wrap_type') === WrapType::GIFT_WRAP_WRAP_TYPE ? __('Wrap Information') : __('Postcard Information');
        $this->setTitle($title);
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab('main', [
            'label'   => __('General'),
            'title'   => __('General'),
            'content' => $this->getChildHtml('main'),
            'active'  => true
        ]);

        return parent::_beforeToHtml();
    }
}
