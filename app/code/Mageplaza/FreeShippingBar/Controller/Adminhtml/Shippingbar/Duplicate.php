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
use Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar;

/**
 * Class Duplicate
 * @package Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar
 */
class Duplicate extends Shippingbar
{
    /**
     * execute action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $shippingBar = $this->_initShippingbar();
            if ($shippingBar) {
                $shippingBar->setId(null)
                    ->save();
            }

            $this->messageManager->addSuccessMessage(__('The Shippingbar has been duplicated.'));
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__(
                'Something went wrong while duplicating shipping bar data. Please review the action log and try again.'
            ));
            $this->logger->critical($e);

            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
        }
    }
}
