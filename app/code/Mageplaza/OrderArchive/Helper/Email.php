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

namespace Mageplaza\OrderArchive\Helper;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Email
 *
 * @package Mageplaza\OrderArchive\Helper
 */
class Email extends Data
{
    const EMAIL_CONFIGURATION = '/email';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Email constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder
    ) {
        $this->transportBuilder = $transportBuilder;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param array $templateParams
     * @param null $storeId
     *
     * @return $this
     */
    public function sendEmailTemplate($templateParams = [], $storeId = null)
    {
        try {
            $toEmails = $this->getToEmail($storeId);

            foreach ($toEmails as $toEmail) {
                $transport = $this->transportBuilder
                    ->setTemplateIdentifier($this->getTemplate($storeId))
                    ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                    ->setTemplateVars($templateParams)
                    ->setFrom($this->getSender($storeId))
                    ->addTo($toEmail)
                    ->getTransport();

                $transport->sendMessage();
            }
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $this;
    }

    /**
     * ======================================= Email Configuration ==================================================
     *
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigEmail($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . self::EMAIL_CONFIGURATION . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabledEmail($storeId = null)
    {
        if ($this->isEnabled()) {
            return (bool) $this->getConfigEmail('enabled', $storeId);
        }

        return false;
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getSender($storeId = null)
    {
        return $this->getConfigEmail('sender', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getTemplate($storeId = null)
    {
        return $this->getConfigEmail('template', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getToEmail($storeId = null)
    {
        return explode(',', $this->getConfigEmail('to', $storeId));
    }
}
