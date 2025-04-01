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

namespace Mageplaza\GiftWrap\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class SyncHistory
 * @package Mageplaza\GiftWrap\Block\Adminhtml\System\Config
 */
class SyncHistory extends Field
{
    /**
     * @var string
     */
    protected $_template = 'system/config/sync-template.phtml';

    /**
     * Unset scope
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();

        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $this->addData([
            'button_label' => $originalData['button_label'],
            'button_url'   => $this->getUrl($originalData['button_url'], ['_current' => true]),
            'html_id'      => $element->getHtmlId(),
        ]);

        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getEstimateUrl()
    {
        return $this->getUrl('mpgiftwrap/wrap_sync/estimatehistory', ['_current' => true]);
    }

    /**
     * @return mixed
     */
    public function getSyncSuccessMessage()
    {
        return __('Wrap History synchronization has been completed.');
    }

    /**
     * @return string
     */
    public function getElementId()
    {
        return 'mp-sync-wrap-history';
    }

    /**
     * @return string
     */
    public function getComponent()
    {
        return 'Mageplaza_GiftWrap/js/sync/wrap-history';
    }
}
