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
 * Class Delete
 * @package Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar
 */
class Delete extends Shippingbar
{
    /**
     * execute action
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('rule_id');
        if (!$id) {
            $this->messageManager->addErrorMessage(__('We cannot find a shipping bar to delete.'));
            $this->_redirect('mpfreeshippingbar/*/');

            return;
        }

        /** @var \Mageplaza\FreeShippingBar\Model\Shippingbar $shippingBar */
        $shippingBar = $this->shippingBarFactory->create()
            ->load($id);

        try {
            $shippingBar->delete();

            $this->messageManager->addSuccessMessage(__('The Shippingbar has been deleted.'));
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while deleting shipping bar data. Please review the action log and try again.')
            );
            $this->logger->critical($e);

            $this->_redirect('*/*/edit', ['id' => $id]);
        }
    }
}
