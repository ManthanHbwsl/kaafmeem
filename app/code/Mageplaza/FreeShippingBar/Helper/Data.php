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

namespace Mageplaza\FreeShippingBar\Helper;

use Magento\Checkout\Model\Cart;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Asset\Repository as AssetFile;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\FreeShippingBar\Model\Shippingbar\Templates;

/**
 * Class Data
 * @package Mageplaza\FreeShippingBar\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'freeshippingbar';

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * Customer session
     *
     * @var HttpContext
     */
    protected $_httpContext;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Cart
     */
    protected $_cart;

    /**
     * Asset service
     *
     * @var AssetFile
     */
    protected $_assetRepo;

    /**
     * @var Templates
     */
    protected $templateModel;

    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param CustomerSession $customerSession
     * @param HttpContext $httpContext
     * @param Image $imageHelper
     * @param Cart $cart
     * @param AssetFile $assetRepo
     * @param Templates $templateModel
     * @param FormatInterface $localeFormat
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        HttpContext $httpContext,
        Image $imageHelper,
        Cart $cart,
        AssetFile $assetRepo,
        Templates $templateModel,
        FormatInterface $localeFormat
    ) {
        $this->_customerSession = $customerSession;
        $this->_httpContext = $httpContext;
        $this->imageHelper = $imageHelper;
        $this->_cart = $cart;
        $this->_assetRepo = $assetRepo;
        $this->templateModel = $templateModel;
        $this->localeFormat = $localeFormat;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP);
    }

    /**
     * get image url in pub/media/mageplaza
     * @return string
     */
    public function getImageMediaMageplazaUrl()
    {
        $baseMediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => 'media']);
        $mageplazaImagePath = $this->imageHelper->getBaseMediaPath(Image::TEMPLATE_MEDIA_TYPE_BACKGROUND);

        return $baseMediaUrl . $mageplazaImagePath;
    }

    /**
     * get image url in view/base/web/mageplaza
     * @return string
     */
    public function getImageFSBMageplazaUrl()
    {
        $imageFSBUrl = $this->_assetRepo->getUrl('Mageplaza_FreeShippingBar::mageplaza/freeshippingbar/background');

        return $imageFSBUrl;
    }

    /**
     * @return mixed
     */
    public function getmpFSBCartTotal()
    {
        return $this->_cart->getQuote()->getSubtotal();
    }

    /**
     * @param string $imageShippingBar
     * @param string $templateIdShippingBar
     *
     * @return string|null
     */
    public function getImageBackgroundUrl($imageShippingBar, $templateIdShippingBar)
    {
        $imagePath = null;
        $templateModel = $this->templateModel->getTemplateModelArray();
        if ($imageShippingBar === null && $templateModel[$templateIdShippingBar]['bg_url_image'] === '') {
            return $imagePath;
        }

        if ($imageShippingBar !== null) {
            $baseMedia = $this->getImageMediaMageplazaUrl();
            $imagePath = $baseMedia . '/' . $imageShippingBar;

            return $imagePath;
        }

        if ($templateIdShippingBar !== null) {
            $baseMedia = $this->getImageFSBMageplazaUrl();
            $imagePath = $baseMedia . $templateModel[$templateIdShippingBar]['bg_url_image'];

            return $imagePath;
        }

        return $imagePath;
    }

    /**
     * @return array
     */
    public function getPriceFormat()
    {
        return $this->localeFormat->getPriceFormat();
    }
}
