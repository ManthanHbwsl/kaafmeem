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

namespace Mageplaza\GiftWrap\Controller\Adminhtml\Category;

use Exception;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mageplaza\GiftWrap\Controller\Adminhtml\AbstractCategory;

/**
 * Class Save
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Category
 */
class Save extends AbstractCategory
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            $object = $this->_initCategory();

            if ($objId = $this->getRequest()->getParam('category_id')) {
                $object->load($objId);
            }

            foreach (['store_id', 'customer_group'] as $item) {
                $data[$item] = isset($data[$item]) ? implode(',', $data[$item]) : null;
            }

            $object->addData($data);

            try {
                $object->save();

                $this->messageManager->addSuccessMessage(__('The category has been saved.'));
                $this->_session->setCategoryData(false);

                if ($this->getRequest()->getParam('back', false)) {
                    return $this->_redirect('*/*/edit', ['id' => $object->getId(), '_current' => true]);
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setCategoryData($data);

                if ($objId) {
                    return $this->_redirect('*/*/edit', ['id' => $objId, '_current' => true]);
                }
            }
        }

        return $this->_redirect('*/*/');
    }
}
