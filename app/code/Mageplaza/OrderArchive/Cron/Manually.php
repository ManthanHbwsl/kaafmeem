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
 * @category  Mageplaza
 * @package   Mageplaza_OrderArchive
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderArchive\Cron;

use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\OrderArchive\Helper\Data as HelperData;
use Mageplaza\OrderArchive\Helper\Email;
use Mageplaza\OrderArchive\Model\ResourceModel\Action;

/**
 * Class Manually
 *
 * @package Mageplaza\OrderArchive\Cron
 */
class Manually
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var Action
     */
    protected $_action;

    /**
     * @var Email
     */
    protected $_email;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Manually constructor.
     *
     * @param HelperData $helperData
     * @param Action $action
     * @param Email $email
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        HelperData $helperData,
        Action $action,
        Email $email,
        StoreManagerInterface $storeManager
    ) {
        $this->_helperData   = $helperData;
        $this->_action       = $action;
        $this->_email        = $email;
        $this->_storeManager = $storeManager;
    }

    /**
     * action cron send email
     */
    public function process()
    {
        foreach ($this->_storeManager->getStores() as $store) {
            $orderIds = $this->_action->getMatchingOrders($store->getId());
            $this->_action->archiveOrder($orderIds);
            if ($this->_email->isEnabledEmail($store->getId()) && count($orderIds) > 0) {
                $templateParams = ['num_order' => count($orderIds)];
                $this->_email->sendEmailTemplate($templateParams, $store->getId());
            }
        }
    }
}
