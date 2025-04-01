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

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\GiftWrap\Controller\Adminhtml\AbstractWrap;
use Mageplaza\GiftWrap\Model\Config\Source\WrapType;

/**
 * Class Edit
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Wrap
 */
class Edit extends AbstractWrap
{
    /**
     * @return Page|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $object = $this->_initWrap();

        if ($objId = $this->getRequest()->getParam('id')) {
            $object->load($objId);

            if (!$object->getId()) {
                $this->messageManager->addErrorMessage(__('This gift wrap no longer exists.'));

                return $this->_redirect('*/*/');
            }
        }

        // restore form data
        $data = $this->_session->getWrapData(true);
        if (!empty($data)) {
            $object->addData($data);
        }

        $this->registry->register('mpgiftwrap_wrap', $object);
        $pageTitle = $this->getRequest()->getParam('wrap_type') === WrapType::GIFT_WRAP_WRAP_TYPE
            ? __('Create New Gift Wrapper') : __('Create New Postcard');

        if ($object) {
            if ($objId) {
                $pageTitle = $object->getWrapType() === WrapType::GIFT_WRAP_WRAP_TYPE
                    ? __('View Gift Wrap #%1', $objId)
                    : __('View Postcard #%1', $objId);
            }
            $resultPage = $this->_initAction();
            $resultPage->getConfig()->getTitle()->prepend($pageTitle);

            return $resultPage;
        }

        return $this->_redirect('*/*/');
    }
}
