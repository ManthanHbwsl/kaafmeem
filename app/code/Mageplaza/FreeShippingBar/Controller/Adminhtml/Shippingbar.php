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

namespace Mageplaza\FreeShippingBar\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTime as Date;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface as TimeZone;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\FreeShippingBar\Helper\Image;
use Mageplaza\FreeShippingBar\Model\ShippingbarFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Shippingbar
 * @package Mageplaza\FreeShippingBar\Controller\Adminhtml
 */
abstract class Shippingbar extends Action
{
    const ADMIN_RESOURCE = 'Mageplaza_FreeShippingBar::shippingbar';

    /**
     * @type PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @type Registry
     */
    protected $_coreRegistry;

    /**
     * @var Image
     */
    protected $_imageHelper;

    /**
     * @var ShippingbarFactory
     */
    protected $shippingBarFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var DateTime
     */
    protected $_dateTime;

    /**
     * @var Date
     */
    protected $date;

    /**
     * @var Date
     */
    protected $timeZone;

    /**
     * Shippingbar constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param Image $imageHelper
     * @param ShippingbarFactory $shippingbarFactory
     * @param LoggerInterface $logger
     * @param DateTime $dateTime
     * @param Date $date
     * @param TimeZone $timeZone
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        Image $imageHelper,
        ShippingbarFactory $shippingbarFactory,
        LoggerInterface $logger,
        DateTime $dateTime,
        Date $date,
        TimeZone $timeZone
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_imageHelper = $imageHelper;
        $this->shippingBarFactory = $shippingbarFactory;
        $this->logger = $logger;
        $this->_dateTime = $dateTime;
        $this->date = $date;
        $this->timeZone = $timeZone;
        parent::__construct($context);
    }

    /**
     * @return \Mageplaza\FreeShippingBar\Model\Shippingbar
     * @throws LocalizedException
     */
    protected function _initShippingbar()
    {
        $shippingbarId = (int)$this->getRequest()->getParam('rule_id');

        /** @var \Mageplaza\FreeShippingBar\Model\Shippingbar $shippingbar */
        $shippingbar = $this->shippingBarFactory->create();
        if ($shippingbarId) {
            $shippingbar->load($shippingbarId);
            if (!$shippingbar->getId()) {
                throw new LocalizedException(__('Item does not exist.'));
            }
        }

        return $shippingbar;
    }
}
