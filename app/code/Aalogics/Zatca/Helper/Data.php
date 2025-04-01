<?php
/**
 *
 * @category   Aalogics
 * @package    Aalogics_Zatca
 * @copyright   Copyright (c) AALOGICS (http://shop.aalogics.com/)
 * @license     https://www.aalogics.com/LICENSE.txt
 */

namespace Aalogics\Zatca\Helper;

use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\Information;
use Magento\Store\Model\StoreManagerInterface;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends AbstractHelper
{
    const CONFIG_MODULE_PATH  = 'zatca/general';

    const QR_IMAGE_NAME = '/zatca_qr.png';
    const QR_IMAGE_FOLDER = '/sales/invoice/qr/';
    
    const POSITION_TOP = 'top';
    const POSITION_MIDDLE = 'middle';
    const POSITION_BOTTOM = 'footer';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Information
     */
    protected $storeInformation;

    /**
     * @var string
     */
    protected $note;

    /**
     * @var ResourceConfig
     */
    protected $resourceConfig;
    
    /**
     * @var \Magento\MediaStorage\Model\ResourceModel\File\Storage\File
     */
    private $_fileUtility;
    
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;
    
    /**
     * 
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $_imageFactory;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param Information $storeInformation
     * @param ObjectManagerInterface $objectManager
     * @param ResourceConfig $resourceConfig
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        Information $storeInformation,
        ObjectManagerInterface $objectManager,
        ResourceConfig $resourceConfig,
        \Magento\MediaStorage\Model\ResourceModel\File\Storage\File $fileUtility,
        \Magento\Framework\Image\AdapterFactory $imageFactory   
    ) {
        $this->filesystem       = $filesystem;
        $this->storeManager     = $storeManager;
        $this->storeInformation = $storeInformation;
        $this->resourceConfig   = $resourceConfig;
        $this->_fileUtility =  $fileUtility;
        $this->_mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory; 

        parent::__construct($context);
    }

    public function getisEnabled( $storeId = null)
    {
       
        return $this->scopeConfig->getValue(self::CONFIG_MODULE_PATH . '/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function getSellerName( $storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_MODULE_PATH . '/seller',  \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function getTaxNumber( $storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_MODULE_PATH . '/tax_number', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function getPosition( $storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_MODULE_PATH . '/position', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function getDetails($storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_MODULE_PATH . '/details', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    public function getQrScale( $storeId = null)
    {
        return $this->scopeConfig->getValue(self::CONFIG_MODULE_PATH . '/qr_scale', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    
    public function getZatcaQRDescription() {
        error_log('zatca description');
        return __('VAT Number').' : '. $this->getTaxNumber($this->storeManager->getStore()->getId()).' '.__($this->getDetails($this->storeManager->getStore()->getId()));
    }
    /**
     * 
     * @param  $invoice
     * @return NULL|mixed
     */
    public function getZatcaQR($invoice = null, $url = false) {
        $image = null;
        if($this->getisEnabled($this->storeManager->getStore()->getId()) && $invoice) {
            $qrOptions = [
                'scale' => $this->getQrScale($this->storeManager->getStore()->getId()),
            ];
            $date = new \DateTime($invoice->getCreatedAt());
            $raw_image = GenerateQrCode::fromArray([
                new Seller($this->getSellerName($this->storeManager->getStore()->getId())), // seller name
                new TaxNumber($this->getTaxNumber($this->storeManager->getStore()->getId())), // seller tax number
                //'2021-07-12T14:25:09Z'
                new InvoiceDate($date->format(\DateTime::ISO8601)), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
                new InvoiceTotalAmount($invoice->getGrandTotal()), // invoice total amount
                new InvoiceTaxAmount($invoice->getTaxAmount()) // invoice tax amount
                // TODO :: Support others tags
            ])->render($qrOptions);
            $image = str_replace('data:image/png;base64,', '', $raw_image);
            $image = str_replace(' ', '+', $image);
            $data = base64_decode($image);
            
            if ($image) {
                $imageFolder = self::QR_IMAGE_FOLDER;
                $imagePath = $imageFolder.'/'.$invoice->getId().'/'.self::QR_IMAGE_NAME;
                $this->_fileUtility->saveFile($imagePath, $data, true);
                if ($this->_mediaDirectory->isFile($imagePath)) {
                    $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
                }
            }
            
        }
        return ($url)?$this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .$imagePath : $image;
    }
    

}
