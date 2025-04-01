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
use Magento\MediaStorage\Model\File\Uploader;
use Mageplaza\GiftWrap\Controller\Adminhtml\AbstractWrap;
use Mageplaza\GiftWrap\Helper\Data;
use Mageplaza\GiftWrap\Model\Config\Source\WrapType;

/**
 * Class Save
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Wrap
 */
class Save extends AbstractWrap
{
    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $this->_uploadImages($data);

            $object = $this->_initWrap();

            if ($objId = $this->getRequest()->getParam('wrap_id')) {
                $object->load($objId);
            }

            foreach (['category'] as $item) {
                $data[$item] = isset($data[$item]) ? implode(',', $data[$item]) : null;
            }

            $object->addData($data);

            try {
                $object->save();

                $wrapType = $object->getWrapType() === WrapType::GIFT_WRAP_WRAP_TYPE ? 'gift wrap' : 'post card';
                $this->messageManager->addSuccessMessage(__('The %1 has been saved.', $wrapType));
                $this->_session->setWrapData(false);

                if ($this->getRequest()->getParam('back', false)) {
                    return $this->_redirect('*/*/edit', ['id' => $object->getId(), '_current' => true]);
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_session->setWrapData($data);

                if ($objId) {
                    return $this->_redirect('*/*/edit', ['id' => $objId, '_current' => true]);
                }
            }
        }

        return $this->_redirect('*/*/');
    }

    /**
     * @param $data
     *
     * @return $this
     */
    protected function _uploadImages(&$data)
    {
        if (!empty($data['image']['delete'])) {
            $data['image'] = '';
        } else {
            try {
                /** @var Uploader $uploader */
                $uploader = $this->data->createObject(Uploader::class, ['fileId' => 'image']);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(true);

                $image = $uploader->save($this->mediaDirectory->getAbsolutePath(Data::TEMPLATE_MEDIA_PATH));

                $data['image'] = Data::TEMPLATE_MEDIA_PATH . '/' . $image['file'];
            } catch (Exception $e) {
                $data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
            }
        }

        return $this;
    }
}
