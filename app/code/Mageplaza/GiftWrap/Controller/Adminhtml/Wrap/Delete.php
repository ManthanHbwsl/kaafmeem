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

namespace Mageplaza\GiftWrap\Controller\Adminhtml\Wrap;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\GiftWrap\Controller\Adminhtml\AbstractWrap;

/**
 * Class Delete
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Wrap
 */
class Delete extends AbstractWrap
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if ($objId = $this->getRequest()->getParam('id')) {
            $object = $this->_initWrap()->load($objId);
            try {
                $object->delete();

                $this->messageManager->addSuccessMessage(__('The gift wrap has been deleted.'));

                return $this->_redirect('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('We can\'t delete the gift wrap right now.'));

                return $this->_redirect('*/*/edit', ['id' => $objId, '_current' => true]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a gift wrap to delete.'));

        return $this->_redirect('*/*/');
    }
}
