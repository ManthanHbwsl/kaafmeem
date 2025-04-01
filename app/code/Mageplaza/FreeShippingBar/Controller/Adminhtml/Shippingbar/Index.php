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

use Magento\Framework\View\Result\Page;
use Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar;

/**
 * Class Index
 * @package Mageplaza\FreeShippingBar\Controller\Adminhtml\Shippingbar
 */
class Index extends Shippingbar
{
    /**
     * execute the action
     *
     * @return \Magento\Backend\Model\View\Result\Page|Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Mageplaza_FreeShippingBar::Shippingbar');
        $resultPage->getConfig()->getTitle()->prepend(__('Free Shipping Bar'));

        return $resultPage;
    }
}
