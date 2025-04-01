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
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\GiftWrap\Controller\Adminhtml\AbstractWrap;
use Mageplaza\GiftWrap\Model\Wrap;

/**
 * Class MassDelete
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Wrap
 */
class MassDelete extends AbstractWrap
{
    /**
     * @return ResponseInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->wrapFactory->create()->getCollection());
        $count = 0;

        /** @var Wrap $item */
        foreach ($collection->getItems() as $item) {
            try {
                $item->delete();
                $count++;
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleting the record(s).'));
            }
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $count));

        return $this->_redirect('*/*/');
    }
}
