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

use Mageplaza\GiftWrap\Controller\Adminhtml\AbstractCategory;

/**
 * Class NewAction
 * @package Mageplaza\GiftWrap\Controller\Adminhtml\Category
 */
class NewAction extends AbstractCategory
{
    /**
     * Forward to edit form
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
