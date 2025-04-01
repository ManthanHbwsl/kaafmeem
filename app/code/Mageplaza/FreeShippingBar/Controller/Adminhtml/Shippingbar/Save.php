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
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar;
use Mageplaza\FreeShippingBar\Helper\Image;

/**
 * Class Save
 * @package Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar
 */
class Save extends Shippingbar
{
    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $data = $this->getRequest()->getPost('shippingbar');
        if (!$data || empty($data)) {
            return $resultRedirect->setPath('*/*/');
        }

        try {
            /** @var \Mageplaza\FreeShippingBar\Model\Shippingbar $shippingbar */
            $shippingbar = $this->_initShippingbar();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('*/*/');
        }

        if ($data['from_date'] && $data['to_date']) {
            if (strtotime($data['from_date']) > strtotime($data['to_date'])) {
                $isEdit = $this->getRequest()->getParam('rule_id');
                $this->getMessageManager()->addErrorMessage(__('Please enter a valid date.'));

                return $isEdit
                    ? $resultRedirect->setPath('*/*/edit', ['rule_id' => $shippingbar->getRuleId()])
                    : $resultRedirect->setPath('*/*/create');
            }
        }

        $this->prepareData($shippingbar, $data);

        $redirectBack = $this->getRequest()->getParam('back', false);

        try {
            $shippingbar->save();

            $this->_getSession()->setData('freeshippingbar_shippingbar_data', false);
            $this->messageManager->addSuccessMessage(__('You saved the shipping bar.'));
        } catch (Exception $e) {
            $this->_getSession()->setData('freeshippingbar_shippingbar_data', $data);
            $redirectBack = true;
            $this->messageManager->addErrorMessage(__('We cannot save the shipping bar.'));
            $this->logger->critical($e);
        }

        return $redirectBack
            ? $resultRedirect->setPath('*/*/edit', ['rule_id' => $shippingbar->getRuleId()])
            : $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $shippingbar
     * @param array $data
     *
     * @throws Exception
     */
    protected function prepareData($shippingbar, $data = [])
    {
        if (!$this->getRequest()->getParam('image')) {
            try {
                $this->_imageHelper->uploadImage(
                    $data,
                    'image',
                    Image::TEMPLATE_MEDIA_TYPE_BACKGROUND,
                    $shippingbar->getImage()
                );
            } catch (Exception $exception) {
                $data['image'] = isset($data['image']['value']) ? $data['image']['value'] : '';
            }
        } else {
            $data['image'] = '';
        }

        /** convert store data*/
        $data['store_id'] = implode(',', $data['store_id']);

        /** convert customer group id data */
        $data['customer_group_id'] = implode(',', $data['customer_group_id']);

        if (isset($data['bar_opacity']) && $data['bar_opacity'] > 1) {
            $data['bar_opacity'] = 1;
        }

        if (isset($data['goal']) && $data['goal'] > 0) {
            $data['goal'] = round($data['goal']);
        }

        $timezone = $this->timeZone;
        $time = $this->_dateTime;

        $data['string_font_connect'] = str_replace(' ', '+', $data['font_text']);
        $data['to_date'] = $data['to_date'] === '' ? null : $time->formatDate($timezone->date($data['to_date'])->format('d.m.Y H:i:s'));
        $data['from_date'] = $data['from_date'] === ''
            ? $timezone->date()->format('d.m.Y H:i:s')
            : $time->formatDate($timezone->date($data['from_date'])->format('d.m.Y H:i:s'));

        $shippingbar->addData($data);
    }
}
