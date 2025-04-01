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

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Status;

/**
 * Class Enable
 * @package Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar
 */
class Enable extends Shippingbar
{
    /**
     * execute action
     *
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        try {
            $shippingbar = $this->_initShippingbar();
            $shippingbar->setStatus(Status::ENABLED)
                ->save();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical($e);

            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);

            return;
        }

        $this->messageManager->addSuccessMessage(__('The Shipping Bar has been enabled.'));
        $this->_redirect('mpfreeshippingbar/*/');
    }
}
