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

use Magento\Sales\Api\Data\InvoiceExtension;
use Magento\Sales\Api\Data\InvoiceExtensionFactory;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

/**
 * Class InvoiceGet
 * @package Mageplaza\GiftWrap\Plugin\Api
 */
class InvoiceGet
{
    /**
     * @var InvoiceExtensionFactory
     */
    protected $extensionFactory;

    /**
     * InvoiceGet constructor.
     *
     * @param InvoiceExtensionFactory $extensionFactory
     */
    public function __construct(InvoiceExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param InvoiceRepositoryInterface $subject
     * @param Collection $resultInvoice
     *
     * @return Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        InvoiceRepositoryInterface $subject,
        Collection $resultInvoice
    ) {
        foreach ($resultInvoice->getItems() as $invoice) {
            $this->afterGet($subject, $invoice);
        }

        return $resultInvoice;
    }

    /**
     * @param InvoiceRepositoryInterface $subject
     * @param InvoiceInterface $resultInvoice
     *
     * @return InvoiceInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(InvoiceRepositoryInterface $subject, InvoiceInterface $resultInvoice)
    {
        $resultInvoice = $this->getInvoiceData($resultInvoice);

        return $resultInvoice;
    }

    /**
     * @param InvoiceInterface $invoice
     *
     * @return InvoiceInterface
     */
    protected function getInvoiceData(InvoiceInterface $invoice)
    {
        $extensionAttributes = $invoice->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getMpGiftWrapAmount()
            && $extensionAttributes->getMpGiftWrapPostcardAmount()) {
            return $invoice;
        }

        /** @var InvoiceExtension $invoiceExtension */
        $invoiceExtension = $extensionAttributes ?: $this->extensionFactory->create();
        if (!$extensionAttributes->getMpGiftWrapAmount()) {
            $invoiceExtension->setMpGiftWrapAmount($invoice->getMpGiftWrapAmount());
            $invoiceExtension->setMpGiftWrapBaseAmount($invoice->getMpGiftWrapBaseAmount());
        }
        if (!$extensionAttributes->getMpGiftWrapPostcardAmount()) {
            $invoiceExtension->setMpGiftWrapPostcardAmount($invoice->getMpGiftWrapPostcardAmount());
            $invoiceExtension->setMpGiftWrapPostcardBaseAmount($invoice->getMpGiftWrapPostcardBaseAmount());
        }
        $invoice->setExtensionAttributes($invoiceExtension);

        return $invoice;
    }
}
