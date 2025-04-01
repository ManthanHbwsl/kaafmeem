<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aalogics\Zatca\Plugin\Order\Pdf;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\Sales\Model\Order\Pdf\Invoice;
use Magento\Sales\Model\Order\Pdf\Config;
use Aalogics\Zatca\Helper\Data;

/**
 * Sales Order Invoice PDF model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ZatcaInvoice extends Invoice
{

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $appEmulation;
    
    protected $productMetadata;
    /**
     * 
     * @var Data
     */
    protected $helper;
    
    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\App\Emulation $appEmulation,
        Data $helper,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
        ) {
        
            $this->helper = $helper;
            $this->_storeManager = $storeManager;
            $this->appEmulation = $appEmulation;
            $this->productMetadata = $productMetadata;
            
            if(version_compare($this->productMetadata->getVersion() , '2.4.0') < 0 ) {
                $appEmulation = $localeResolver;
            }
            
            
            parent::__construct(
                $paymentData,
                $string,
                $scopeConfig,
                $filesystem,
                $pdfConfig,
                $pdfTotalFactory,
                $pdfItemsFactory,
                $localeDate,
                $inlineTranslation,
                $addressRenderer,
                $storeManager,
                $appEmulation,
                $data
                );
           
    }
    
    public function afterGetPdf(\Magento\Sales\Model\Order\Pdf\Invoice $subject, $result, $invoices = []) {
        
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');
        
        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        
        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                if(version_compare($this->productMetadata->getVersion() , '2.4.0') >= 0 ) {
                $this->appEmulation->startEnvironmentEmulation(
                    $invoice->getStoreId(),
                    \Magento\Framework\App\Area::AREA_FRONTEND,
                    true
                    );
                }
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            $position = $this->helper->getPosition();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            switch ($position) {
                case \Aalogics\Zatca\Helper\Data::POSITION_TOP:
                    $this->insertZatcaQr($page, $invoice->getStore(), $invoice);
                   
                    /* Add head */
                    $this->insertOrder(
                        $page,
                        $order,
                        $this->_scopeConfig->isSetFlag(
                            self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                            $order->getStoreId()
                            )
                        );
                    /* Add document text and number */
                    $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
                    /* Add table */
                    $this->_drawHeader($page);
                    /* Add body */
                    foreach ($invoice->getAllItems() as $item) {
                        if ($item->getOrderItem()->getParentItem()) {
                            continue;
                        }
                        /* Draw item */
                        $this->_drawItem($item, $page, $order);
                        $page = end($pdf->pages);
                    }
                    /* Add totals */
                    $this->insertTotals($page, $invoice);
                    $this->y = $this->y ? $this->y : 815;
                    break;
                case \Aalogics\Zatca\Helper\Data::POSITION_MIDDLE:
                   
                    /* Add head */
                    $this->insertOrder(
                        $page,
                        $order,
                        $this->_scopeConfig->isSetFlag(
                            self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                            $order->getStoreId()
                            )
                        );
                    $this->y = $this->y + 10;
                    $this->insertZatcaQr($page, $invoice->getStore(), $invoice, $this->y);
                    /* Add document text and number */
                    $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
                    /* Add table */
                    $this->_drawHeader($page);
                    /* Add body */
                    foreach ($invoice->getAllItems() as $item) {
                        if ($item->getOrderItem()->getParentItem()) {
                            continue;
                        }
                        /* Draw item */
                        $this->_drawItem($item, $page, $order);
                        $page = end($pdf->pages);
                    }
                    /* Add totals */
                    $this->insertTotals($page, $invoice);
                    break;
                    
                case \Aalogics\Zatca\Helper\Data::POSITION_BOTTOM:
                    
                    /* Add head */
                    $this->insertOrder(
                        $page,
                        $order,
                        $this->_scopeConfig->isSetFlag(
                            self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                            $order->getStoreId()
                            )
                        );
                    /* Add document text and number */
                    $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
                    /* Add table */
                    $this->_drawHeader($page);
                    /* Add body */
                    foreach ($invoice->getAllItems() as $item) {
                        if ($item->getOrderItem()->getParentItem()) {
                            continue;
                        }
                        /* Draw item */
                        $this->_drawItem($item, $page, $order);
                        $page = end($pdf->pages);
                    }
                    /* Add totals */
                    $page = $this->insertTotals($page, $invoice);
                    $this->y = $this->y - 10;
                    $this->insertZatcaQr($page, $invoice->getStore(), $invoice, $this->y);
                    break;
            }
            if(version_compare($this->productMetadata->getVersion() , '2.4.0') >= 0 ) {
                if ($invoice->getStoreId()) {
                    $this->appEmulation->stopEnvironmentEmulation();
                }
            }
        }
        $this->_afterGetPdf();
        return $pdf;
        
    }

    
    public function insertZatcaQr(&$page, $store = null, $invoice = null,  $top = 730) {
        
        $image = $this->helper->getZatcaQR($invoice);
        $description = $this->helper->getZatcaQRDescription();
        //top border of the page
        $widthLimit = 270;
        //half of the page width
        $heightLimit = 270;

        $x1 = 525;
        
        if ($image) {
            
            //assuming the image is not a "skyscraper"
            $width = $image->getPixelWidth();
            $height = $image->getPixelHeight();
            //preserving aspect ratio (proportions)
            $ratio = $width / $height;
            if ($ratio > 1 && $width > $widthLimit) {
                $width = $widthLimit;
                $height = $width / $ratio;
            } elseif ($ratio < 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $height * $ratio;
            } elseif ($ratio == 1 && $height > $heightLimit) {
                $height = $heightLimit;
                $width = $widthLimit;
            }
            
            $x2 = 560;
            $x1 = $x2 - $width;
            $y1 = $top - $height;
            $y2 = $top;
            
            //coordinates after transformation are rounded by Zend
            $page->drawImage($image, $x1, $y1, $x2, $y2);
            
            $this->y = $y1 - 10;
           }
           
           if($description) {
                $descriptions = explode('/', $description);
               foreach ($descriptions as $key => $value) {
                   $x1 = -260;
                   $top = $this->y + $height/2;
                   $font = $this->_setFontRegular($page, 10);
                   $page->drawText(
                       strip_tags($value),
                       $this->getAlignRight($value, $x1, 440, $font, 10),
                       $top,
                       'UTF-8'
                       );
                   
                   $this->y = $this->y - 10;
               }
               
           }
    }
    
}
