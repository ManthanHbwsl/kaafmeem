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

namespace Mageplaza\GiftWrap\Plugin\Api;

use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoExtension;
use Magento\Sales\Api\Data\CreditmemoExtensionFactory;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection;

/**
 * Class CreditmemoGet
 * @package Mageplaza\GiftWrap\Plugin\Api
 */
class CreditmemoGet
{
    /**
     * @var CreditmemoExtensionFactory
     */
    protected $extensionFactory;

    /**
     * CreditmemoGet constructor.
     *
     * @param CreditmemoExtensionFactory $extensionFactory
     */
    public function __construct(CreditmemoExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param CreditmemoRepositoryInterface $subject
     * @param Collection $resultCreditmemo
     *
     * @return Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(CreditmemoRepositoryInterface $subject, Collection $resultCreditmemo)
    {
        foreach ($resultCreditmemo->getItems() as $creditmemo) {
            $this->afterGet($subject, $creditmemo);
        }

        return $resultCreditmemo;
    }

    /**
     * @param CreditmemoRepositoryInterface $subject
     * @param CreditmemoInterface $resultCreditmemo
     *
     * @return CreditmemoInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(CreditmemoRepositoryInterface $subject, CreditmemoInterface $resultCreditmemo)
    {
        $resultCreditmemo = $this->getCreditmemoData($resultCreditmemo);

        return $resultCreditmemo;
    }

    /**
     * @param CreditmemoInterface $creditmemo
     *
     * @return CreditmemoInterface
     */
    protected function getCreditmemoData(CreditmemoInterface $creditmemo)
    {
        $extensionAttributes = $creditmemo->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getMpGiftWrapAmount()
            && $extensionAttributes->getMpGiftWrapPostcardAmount()) {
            return $creditmemo;
        }

        /** @var CreditmemoExtension $creditmemoExtension */
        $creditmemoExtension = $extensionAttributes ?: $this->extensionFactory->create();
        if (!$extensionAttributes->getMpGiftWrapAmount()) {
            $creditmemoExtension->setMpGiftWrapAmount($creditmemo->getMpGiftWrapAmount());
            $creditmemoExtension->setMpGiftWrapBaseAmount($creditmemo->getMpGiftWrapBaseAmount());
        }
        if (!$extensionAttributes->getMpGiftWrapPostcardAmount()) {
            $creditmemoExtension->setMpGiftWrapPostcardAmount($creditmemo->getMpGiftWrapPostcardAmount());
            $creditmemoExtension->setMpGiftWrapPostcardBaseAmount($creditmemo->getMpGiftWrapPostcardBaseAmount());
        }
        $creditmemo->setExtensionAttributes($creditmemoExtension);

        return $creditmemo;
    }
}
