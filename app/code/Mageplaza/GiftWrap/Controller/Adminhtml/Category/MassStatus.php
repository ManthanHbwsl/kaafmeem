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
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\GiftWrap\Controller\Adminhtml\AbstractCategory;
use Mageplaza\GiftWrap\Model\Category;

/**
 * Class MassStatus
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Category
 */
class MassStatus extends AbstractCategory
{
    /**
     * @return ResponseInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->categoryFactory->create()->getCollection());
        $status = (int)$this->getRequest()->getParam('status');
        $count = 0;

        /** @var Category $item */
        foreach ($collection->getItems() as $item) {
            try {
                $item->setStatus($status);
                $item->save();
                $count++;
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while updating the record(s) status.'));
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $count));

        return $this->_redirect('*/*/');
    }
}
