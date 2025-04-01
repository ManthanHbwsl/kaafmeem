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

namespace Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar;

/**
 * Class Edit
 * @package Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar
 */
class Edit extends Shippingbar
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        try {
            $shippingbar = $this->_initShippingbar();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            $this->_redirect('*/*/');

            return;
        }

        $data = $this->_getSession()->getData('freeshippingbar_shippingbar_data', true);
        if (!empty($data)) {
            /** convert store data*/
            $storeData = explode(',', $data['store_id']);
            $data['store_id'] = $storeData;

            $shippingbar->addData($data);
        }

        $this->_coreRegistry->register('current_shippingbar', $shippingbar);

        $this->_view->loadLayout();
        $this->_setActiveMenu('Mageplaza_FreeShippingBar::shippingbar');
        $title = $shippingbar->getId() ? $shippingbar->getName() : __('New Free Shipping Bar');
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);

        $this->_view->renderLayout();
    }
}
