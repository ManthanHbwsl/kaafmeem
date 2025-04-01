<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\GiftWrap\Helper;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item as CreditmemoItem;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection as CreditmemoCollection;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection as InvoiceCollection;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\GiftWrap\Api\Data\WrapDetailsInterface as WrapItf;
use Mageplaza\GiftWrap\Model\Category;
use Mageplaza\GiftWrap\Model\CategoryFactory;
use Mageplaza\GiftWrap\Model\Config\Source\GiftMessageScope;
use Mageplaza\GiftWrap\Model\Config\Source\Price;
use Mageplaza\GiftWrap\Model\Config\Source\Status;
use Mageplaza\GiftWrap\Model\Config\Source\Type;
use Mageplaza\GiftWrap\Model\ResourceModel\Wrap\CollectionFactory;
use Mageplaza\GiftWrap\Model\WrapFactory;
use Mageplaza\GiftWrap\Model\ResourceModel\History\CollectionFactory as HistoryCollection;
use Mageplaza\GiftWrap\Model\ResourceModel\History as HistoryResource;
use Mageplaza\GiftWrap\Model\Config\Source\WrapType;

/**
 * Class Data
 * @package Mageplaza\GiftWrap\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH      = 'mpgiftwrap';
    const TEMPLATE_MEDIA_PATH     = 'mageplaza/giftwrap';
    const GIFT_WRAP_DATA          = 'mp_gift_wrap_data';
    const GIFT_WRAP_POSTCARD_DATA = 'mp_gift_wrap_postcard_data';
    const ALLOWED_TYPE            = ['simple', 'configurable', 'bundle'];

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;

    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var string
     */
    protected $placeHolderImage;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var CollectionFactory
     */
    protected $wrapCollectionFactory;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var WrapFactory
     */
    protected $wrapFactory;

    /**
     * @var FormatInterface
     */
    private $localFormat;

    /**
     * @var HistoryCollection
     */
    private $historyCollection;

    /**
     * @var HistoryResource
     */
    private $historyResource;

    private $wrapKeys = [];

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Filesystem $filesystem
     * @param Repository $assetRepo
     * @param PriceCurrencyInterface $priceCurrency
     * @param CategoryFactory $categoryFactory
     * @param CollectionFactory $wrapCollectionFactory
     * @param CustomerSession $customerSession
     * @param Escaper $escaper
     * @param HttpContext $httpContext
     * @param WrapFactory $wrapFactory
     * @param FormatInterface $localFormat
     * @param HistoryCollection $historyCollection
     * @param HistoryResource $historyResource
     *
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        Repository $assetRepo,
        PriceCurrencyInterface $priceCurrency,
        CategoryFactory $categoryFactory,
        CollectionFactory $wrapCollectionFactory,
        CustomerSession $customerSession,
        Escaper $escaper,
        HttpContext $httpContext,
        WrapFactory $wrapFactory,
        FormatInterface $localFormat,
        HistoryCollection $historyCollection,
        HistoryResource $historyResource
    ) {
        $this->filesystem            = $filesystem;
        $this->mediaDirectory        = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->assetRepo             = $assetRepo;
        $this->priceCurrency         = $priceCurrency;
        $this->categoryFactory       = $categoryFactory;
        $this->wrapCollectionFactory = $wrapCollectionFactory;
        $this->customerSession       = $customerSession;
        $this->escaper               = $escaper;
        $this->httpContext           = $httpContext;
        $this->wrapFactory           = $wrapFactory;
        $this->localFormat           = $localFormat;
        $this->historyCollection     = $historyCollection;
        $this->historyResource       = $historyResource;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * Decode function for testing possibility
     *
     * @param string $encodedValue
     *
     * @return mixed
     */
    public function jsonDecodeData($encodedValue)
    {
        return self::jsonDecode($encodedValue);
    }

    /**
     * Encode function for testing possibility
     *
     * @param mixed $valueToEncode
     *
     * @return string
     */
    public function jsonEncodeData($valueToEncode)
    {
        return self::jsonEncode($valueToEncode);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getAddLabel($store = null)
    {
        return $this->getConfigGeneral('add_label', $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getChangeLabel($store = null)
    {
        return $this->getConfigGeneral('change_label', $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getTaxClass($store = null)
    {
        return $this->getConfigGeneral('tax_class', $store);
    }

    /**
     * @param null $store
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getGiftWrapIcon($store = null)
    {
        $value = $this->getConfigGeneral('gift_wrap_icon', $store);

        return $value ? $this->getMediaUrl($value) : null;
    }

    /**
     * @param null $store
     *
     * @return int
     */
    public function getGiftWrapType($store = null)
    {
        return (int) $this->getConfigGeneral('gift_wrap_type', $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getNoticeForCustomer($store = null)
    {
        return $this->getConfigGeneral('customer_notice', $store);
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isShowOnProduct($store = null)
    {
        return $this->getGiftWrapType($store) !== Type::ALL
            && (bool) $this->getConfigGeneral('show_on_product', $store);
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isShowAllCart($store = null)
    {
        return $this->getGiftWrapType($store) !== Type::ITEM;
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isEnabledGiftMessage($store = null)
    {
        return (bool) $this->getConfigGeneral('gift_message', $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getMaxCharacters($store = null)
    {
        $maxCharacters = (int) $this->getConfigGeneral('max_characters', $store);

        return $maxCharacters ?: 120;
    }

    /**
     * @param bool $convert
     * @param bool $format
     * @param null $store
     *
     * @return string|float
     */
    public function getGiftMessageFee($convert = true, $format = true, $store = null)
    {
        $value = $this->getConfigGeneral('gift_message_fee', $store);
        if ($convert) {
            return $this->convertPrice($value, $format, false);
        }

        return $format ? $this->formatPrice($value, false) : $value;
    }

    /**
     * @param int $page
     * @param null $storeId
     *
     * @return bool
     */
    public function getGiftMessageVisible($page = GiftMessageScope::CART, $storeId = null)
    {
        $config = explode(',', (string)$this->getConfigGeneral('gift_message_visible', $storeId));

        return in_array($page, $config, false);
    }

    /**
     * @return WriteInterface
     */
    public function getMediaDirectory()
    {
        return $this->mediaDirectory;
    }

    /**
     * @return string
     */
    public function getPlaceHolderImage()
    {
        if ($this->placeHolderImage === null) {
            $this->placeHolderImage = $this->assetRepo->getUrl('Magento_Catalog::images/product/placeholder/image.jpg');
        }

        return $this->placeHolderImage;
    }

    /**
     * @param bool $withPath
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseMediaUrl($withPath)
    {
        /** @var Store $store */
        $store = $this->storeManager->getStore();

        if ($withPath) {
            return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::TEMPLATE_MEDIA_PATH . '/';
        }

        return $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param $file
     * @param bool $withPath
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getMediaUrl($file, $withPath = true)
    {
        return $this->getBaseMediaUrl($withPath) . $this->_prepareFile($file);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * @param float $amount
     * @param bool $includeContainer
     * @param null $scope
     * @param null $currency
     * @param int $precision
     *
     * @return float
     */
    public function formatPrice(
        $amount,
        $includeContainer = true,
        $scope = null,
        $currency = null,
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION
    ) {
        return $this->priceCurrency->format($amount, $includeContainer, $precision, $scope, $currency);
    }

    /**
     * @param float $amount
     * @param bool $format
     * @param bool $includeContainer
     * @param null $scope
     *
     * @return float|string
     */
    public function convertPrice($amount, $format = true, $includeContainer = true, $scope = null)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat(
                $amount,
                $includeContainer,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $scope
            )
            : $this->priceCurrency->convert($amount, $scope);
    }

    /**
     * @param array $category
     * @param int|null $storeId
     * @param int|null $groupId
     *
     * @return bool
     * @throws LocalizedException
     */
    protected function isDisabledCategory($category, $storeId = null, $groupId = null)
    {
        $storeId = $this->getScopeId($storeId);
        $groupId = $this->getGroupId($groupId);

        $stores = explode(',', $category['store_id'] ?: 0);
        $groups = explode(',', $category['customer_group'] ?: 0);

        $isVisibleStore = in_array(0, $stores, false) || in_array($storeId, $stores, false);
        $isVisibleGroup = in_array($groupId, $groups, false);

        $wraps     = $this->wrapCollectionFactory->create()
            ->addFieldToFilter('status', Status::ENABLE)
            ->addFieldToFilter('category', ['finset' => $category['category_id']]);
        $wrapCount = $wraps->getSize();

        return !$isVisibleStore || !$isVisibleGroup || (int) $category['status'] !== Status::ENABLE || !$wrapCount;
    }

    /**
     * @param int|null $storeId
     * @param int|null $groupId
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCategories($storeId = null, $groupId = null)
    {
        $collection = $this->categoryFactory->create()->getCollection();

        $result = $collection->getData();

        usort($result, function ($valueA, $valueB) {
            return ($valueA['sort_order'] <= $valueB['sort_order']) ? -1 : 1;
        });

        foreach ($result as $index => $item) {
            if ($this->isDisabledCategory($item, $storeId, $groupId)) {
                unset($result[$index]);
            }
        }

        $wraps = $this->wrapCollectionFactory->create()
            ->addFieldToFilter('status', Status::ENABLE)
            ->addFieldToFilter('category', ['null' => true]);
        if ($wraps->getSize()) {
            $result[] = ['category_id' => 'other', 'name' => __('Others')];
        }

        return self::jsonEncode(array_values($result));
    }

    /**
     * @param array $wrap
     *
     * @param null $storeId
     * @param null $groupId
     *
     * @return bool
     * @throws LocalizedException
     */
    protected function isDisabledWrap($wrap, $storeId = null, $groupId = null)
    {
        if (empty($wrap['category'])) {
            return (int) $wrap['status'] !== Status::ENABLE;
        }

        $disableByCategory = true;

        foreach (explode(',', $wrap['category']) as $cateId) {
            /** @var Category $category */
            $category = $this->categoryFactory->create()->load($cateId);

            if ($category->getId() && !$this->isDisabledCategory($category->getData(), $storeId, $groupId)) {
                $disableByCategory = false;
            }
        }

        return (int) $wrap['status'] !== Status::ENABLE || $disableByCategory;
    }

    /**
     * @param array $wrap
     * @param bool $isShowException
     *
     * @return bool
     * @throws LocalizedException
     */
    public function validateWrap($wrap, $isShowException = true)
    {
        $wrapModel = $this->wrapFactory->create()->load($wrap['wrap_id']);
        $result    = true;

        if (!$wrapModel->getId() || ($wrapModel->getCategory() && $this->isDisabledWrap($wrap))) {
            $result = false;
        }

        if (!$result && $isShowException) {
            throw new LocalizedException(__('Invalid Gift Wrap. Please reload page and try again!'));
        }

        return $result;
    }

    /**
     * @param string|null $wrapType
     * @param null $storeId
     * @param null $groupId
     *
     * @return string
     * @throws LocalizedException
     */
    public function getWraps($wrapType = null, $storeId = null, $groupId = null)
    {
        $collection = $this->wrapCollectionFactory->create();
        if ($wrapType) {
            $collection->addAttributeToFilter('wrap_type', ['eq' => $wrapType]);
        }
        $result = $collection->getData();

        usort($result, function ($valueA, $valueB) {
            return ($valueA['sort_order'] <= $valueB['sort_order']) ? -1 : 1;
        });

        foreach ($result as $index => &$wrap) {
            if ($this->isDisabledWrap($wrap, $storeId, $groupId)) {
                unset($result[$index]);
                continue;
            }

            $this->processWrap($wrap);
        }

        return self::jsonEncode(array_values($result));
    }

    /**
     * @param array $wrap
     *
     * @throws NoSuchEntityException
     */
    public function processWrap(&$wrap)
    {
        $wrap['image']    = empty($wrap['image'])
            ? $this->getPlaceHolderImage()
            : $this->getMediaUrl($wrap['image'], false);
        $wrap['price']    = $this->convertPrice($wrap['amount'], true, false);
        $wrap['category'] = empty($wrap['category']) ? 'other' : $wrap['category'];
    }

    /**
     * @param Item $item
     * @param array $result
     *
     * @return array
     */
    public function getOrderOptions($item, $result)
    {
        $wrapData     = self::jsonDecode($item->getData(self::GIFT_WRAP_DATA));
        $postcardData = self::jsonDecode($item->getData(self::GIFT_WRAP_POSTCARD_DATA));

        return array_merge(
            $this->orderOptionsData($result, $wrapData, self::GIFT_WRAP_DATA),
            $this->orderOptionsData($result, $postcardData, self::GIFT_WRAP_POSTCARD_DATA)
        );
    }

    /**
     * @param array $result
     * @param array|mixed $data
     *
     * @return array
     */
    private function orderOptionsData($result, $data, $wrapDataType)
    {
        $options = [];
        if (empty($data)) {
            return array_merge($result, $options);
        }

        if (!empty($data[WrapItf::ALL_CART])) {
            $options[] = [
                'label'       => $wrapDataType === self::GIFT_WRAP_DATA ? __('Gift Wrap (All Cart)') : __('Gift Postcard (All Cart)'),
                'value'       => $data[WrapItf::NAME],
                'custom_view' => true
            ];

            if (!empty($data[WrapItf::USE_GIFT_MESSAGE])) {
                $options[] = [
                    'label'       => __('Gift Note (All Cart)'),
                    'value'       => $this->escaper->escapeHtml($data[WrapItf::GIFT_MESSAGE]),
                    'custom_view' => true
                ];
            }

            if ($wrapDataType === self::GIFT_WRAP_POSTCARD_DATA && $data[WrapItf::GIFT_MESSAGE]) {
                $options[] = [
                    'label'       => __('Gift Message (All Cart)'),
                    'value'       => $this->escaper->escapeHtml($data[WrapItf::GIFT_MESSAGE]),
                    'custom_view' => true
                ];
            }

            return array_merge($result, $options);
        }

        $options[] = [
            'label'       => $wrapDataType === self::GIFT_WRAP_DATA ? __('Gift Wrap') : __('Gift Postcard'),
            'value'       => $data[WrapItf::NAME],
            'custom_view' => true
        ];

        if (!empty($data[WrapItf::USE_GIFT_MESSAGE])) {
            $options[] = [
                'label'       => __('Gift Note'),
                'value'       => $this->escaper->escapeHtml($data[WrapItf::GIFT_MESSAGE]),
                'custom_view' => true
            ];
        }

        if ($wrapDataType === self::GIFT_WRAP_POSTCARD_DATA) {
            $options[] = [
                'label'       => __('Gift Message'),
                'value'       => $this->escaper->escapeHtml($data[WrapItf::GIFT_MESSAGE]),
                'custom_view' => true
            ];
        }

        return array_merge($result, $options);
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isOscPage($store = null)
    {
        return $this->isEnabled($store) && $this->_request->getRouteName() === 'onestepcheckout';
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     * @throws LocalizedException
     */
    public function getScopeId($storeId = null)
    {
        if ($storeId) {
            return $storeId;
        }

        $scope = $this->_request->getParam(ScopeInterface::SCOPE_STORE) ?: $this->storeManager->getStore()->getId();

        if ($websiteId = $this->_request->getParam(ScopeInterface::SCOPE_WEBSITE)) {
            /** @var Website $website */
            $website = $this->storeManager->getWebsite($websiteId);
            $scope   = $website->getDefaultStore()->getId();
        }

        return $scope;
    }

    /**
     * @param int|null $groupId
     *
     * @return int
     */
    public function getGroupId($groupId = null)
    {
        if ($groupId) {
            return $groupId;
        }

        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer()->getGroupId();
        }

        /**
         * Get customer group when full page cache enable
         */
        if ($this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            return $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP);
        }

        return 0;
    }

    /**
     * @param Invoice|Creditmemo $sales
     *
     * @return float
     */
    public function getAppliedAmount($sales)
    {
        $amount = 0;

        $order = $sales->getOrder();
        if ($sales->getEntityType() === 'invoice') {
            $collection = $order->getInvoiceCollection();
        } else {
            $collection = $order->getCreditmemosCollection();
        }

        foreach ($collection as $item) {
            $amount += $item->getMpGiftWrapAmount();
        }

        return $amount;
    }

    /**
     * @param Invoice|Creditmemo $sales
     *
     * @return float
     */
    public function getAppliedBaseAmount($sales)
    {
        $discount = $this->getAppliedAmount($sales);

        $rate = $this->convertPrice(1, false, false, $sales->getStoreId());

        return $discount / $rate;
    }

    /**
     * @param int|null $storeId
     *
     * @return array|mixed
     */
    public function getDescription($storeId = null)
    {
        return $this->getConfigGeneral('description', $storeId);
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getPriceFormat()
    {
        $code = $this->storeManager->getStore()->getBaseCurrencyCode();

        return $this->localFormat->getPriceFormat(null, $code);
    }

    /**
     * @param array $wraps
     * @param Item $item
     * @param array|mixed $data
     * @param int $orderId
     * @param float $amount
     */
    public function setWraps(&$wraps, $item, $data, $orderId, $amount)
    {
        $wrapType          = $data[WrapItf::WRAP_TYPE] ?? WrapType::GIFT_WRAP_WRAP_TYPE;
        $allCartIdentifier = $data['price_type'] == \Mageplaza\GiftWrap\Model\Config\Source\Price::BY_QTY
            ? $item->getId() . '_' . $data[WrapItf::WRAP_ID] . '_cart' : $data[WrapItf::WRAP_ID] . '_cart';
        $identifier        = empty($data[WrapItf::ALL_CART])
            ? $item->getId() . '_' . $wrapType . '_item'
            : $allCartIdentifier;

        $wraps[$identifier]['wrap_id']  = $data[WrapItf::WRAP_ID];
        $wraps[$identifier]['order_id'] = $orderId;
        $wraps[$identifier]['amount']   = $amount;

        if (empty($wraps[$identifier]['product_list'])) {
            $wraps[$identifier]['product_list'] = [];
        }

        $name = $item->getName();

        if (is_string($simpleName = $item->getProductOptionByCode('simple_name'))) {
            $name = $simpleName;
        }

        $wraps[$identifier]['product_list'][] = [
            'id'   => $item->getId(),
            'name' => $name,
            'qty'  => (float) $item->getQtyOrdered()
        ];
    }

    /**
     * @param Invoice|Creditmemo $object
     * @param array $historyData
     *
     * @throws Exception
     */
    public function updateWrapHistoryData($object, &$historyData)
    {
        $taxRate = $object->getOrder()->getMpGiftWrapTax();
        $storeId = $object->getStoreId();
        $wraps   = [];

        $postcardAllCartMultiplier = 0;
        $postcardAllCartAmount     = $postcardAllCartMultiplier;
        $postcardAmount            = $postcardAllCartAmount;
        $allCartMessageFee         = $postcardAmount;
        $allCartMultiplier         = $allCartMessageFee;
        $allCartAmount             = $allCartMultiplier;
        $amount                    = $allCartAmount;

        foreach ($object->getAllItems() as $item) {
            if (!($orderItem = $item->getOrderItem())) {
                continue;
            }
            $wrapAmountData     = [
                'amount'              => $amount,
                'all_cart_amount'     => $allCartAmount,
                'all_cart_multiplier' => $allCartMultiplier,
                'tax_rate'            => $taxRate,
            ];
            $postcardAmountData = [
                'amount'              => $postcardAmount,
                'all_cart_amount'     => $postcardAllCartAmount,
                'all_cart_multiplier' => $postcardAllCartMultiplier,
                'tax_rate'            => $taxRate,
            ];

            $product  = $orderItem->getProduct();
            $itemData = [
                'order_item' => $orderItem,
                'item'       => $item,
                'product'    => $product,
                'store_id'   => $storeId,
                'order_id'   => $object->getOrderId(),
            ];

            [$amount, $allCartAmount, $allCartMultiplier] = $this->setWrapsAfterCalculate(
                $itemData,
                $wrapAmountData,
                self::GIFT_WRAP_DATA,
                $wraps
            );

            [$postcardAmount, $postcardAllCartAmount, $postcardAllCartMultiplier] =
                $this->setWrapsAfterCalculate(
                    $itemData,
                    $postcardAmountData,
                    self::GIFT_WRAP_POSTCARD_DATA,
                    $wraps
                );
        }

        $this->updateHistoryData($wraps, $object, $historyData);
        $this->updateHistoryData($wraps, $object, $historyData);
    }

    /**
     * @param array $data
     * @param array $amountData
     * @param string $wrapDataType
     * @param array $wraps
     *
     * @return array
     */
    public function setWrapsAfterCalculate($data, $amountData, $wrapDataType, &$wraps)
    {
        $orderItem         = $data['order_item'];
        $item              = $data['item'];
        $product           = $data['product'];
        $storeId           = $data['store_id'];
        $orderId           = $data['order_id'];
        $amount            = $amountData['amount'];
        $allCartAmount     = $amountData['all_cart_amount'];
        $allCartMultiplier = $amountData['all_cart_multiplier'];
        $taxRate           = $amountData['tax_rate'];

        $wrapData = self::jsonDecode($orderItem->getData($wrapDataType));
        if (!$this->notAllowCalculateItem($wrapData, $orderItem, $item, $product, $storeId)) {
            if (!empty($data[WrapItf::ALL_CART])) {
                [
                    $allCartAmount,
                    $allCartMultiplier,
                    $amountAll
                ] = $this->calculateAmountAllCart(
                    $wrapData,
                    $item,
                    $allCartMultiplier,
                    $taxRate
                );

                $this->setWraps($wraps, $orderItem, $wrapData, $orderId, $amountAll);
            } else {
                [$amount, $wrapAmount] = $this->calculateWrapAmountItem(
                    $wrapData,
                    $item,
                    $taxRate,
                    $amount
                );

                $this->setWraps($wraps, $orderItem, $wrapData, $orderId, $wrapAmount);
            }
        }

        return [$amount, $allCartAmount, $allCartMultiplier];
    }

    /**
     * @param array $wraps
     * @param Invoice|Creditmemo $object
     * @param array $historyData
     *
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function updateHistoryData($wraps, $object, &$historyData)
    {
        foreach ($wraps as $key => &$wrap) {
            if (in_array($key, $this->wrapKeys, true)) {
                continue;
            }
            $this->wrapKeys[]     = $key;
            $wrap['product_list'] = Data::jsonEncode($wrap['product_list']);
            /** @var \Mageplaza\GiftWrap\Model\ResourceModel\History\Collection $historyCollection */
            $historyCollection = $this->historyCollection->create();
            $productList       = substr($wrap['product_list'], 2, -2);
            $historyCollection->addFieldToFilter('wrap_id', $wrap['wrap_id'])
                ->addFieldToFilter('order_id', $wrap['order_id']);
            $historyCollection->getSelect()->where(
                "product_list LIKE '%{$productList}%'"
            );

            $wrapHistory = $historyCollection->getFirstItem();
            if ($historyId = $wrapHistory->getHistoryId()) {
                $amountKey         = $object instanceof Invoice
                    ? 'mp_gift_wrap_invoice_amount' : 'mp_gift_wrap_creditmemo_amount';
                $baseAmountKey     = $object instanceof Invoice
                    ? 'mp_gift_wrap_invoice_base_amount' : 'mp_gift_wrap_creditmemo_base_amount';
                $historyBaseAmount = &$historyData[$historyId][$baseAmountKey];
                $historyBaseAmount = isset($historyBaseAmount) ?
                    ($historyBaseAmount + $wrap['amount']) : $wrap['amount'];

                $amountConverted = $this->convertPrice(
                    $wrap['amount'],
                    false,
                    false,
                    $object->getStore()
                );
                $historyAmount   =  &$historyData[$historyId][$amountKey];
                $historyAmount   = isset($historyAmount) ?
                    ($historyAmount + $amountConverted) : $amountConverted;
            }
        }
    }

    /**
     * @param array $data
     * @param Item $orderItem
     * @param InvoiceItem|CreditmemoItem $item
     * @param Product $product
     * @param int $storeId
     *
     * @return bool
     */
    public function notAllowCalculateItem($data, $orderItem, $item, $product, $storeId)
    {
        return empty($data)
            || $orderItem->getParentItem()
            || $item->getQty() === 0
            || ($product && $product->isVirtual())
            || (!empty($data['all_cart']) && !$this->isShowAllCart($storeId))
            || (empty($data['all_cart']) && !$this->isShowOnProduct($storeId));
    }

    /**
     * @param array $data
     * @param InvoiceItem|CreditmemoItem $item
     * @param int $allCartMultiplier
     * @param float $taxRate
     *
     * @return array
     */
    public function calculateAmountAllCart($data, $item, $allCartMultiplier, $taxRate)
    {
        $allCartAmount     = $data[WrapItf::AMOUNT];
        $allCartMultiplier += (int) $data[WrapItf::PRICE_TYPE] === Price::BY_QTY ? $item->getQty() : 0;
        $cartMultiplier    = $allCartMultiplier ?: 1;
        $amountAll         = $allCartAmount * $cartMultiplier;
        $amountAll         *= (1 + $taxRate / 100);

        return [$allCartAmount, $allCartMultiplier, $amountAll];
    }

    /**
     * @param array $data
     * @param InvoiceItem|CreditmemoItem $item
     * @param float $taxRate
     * @param float $amount
     *
     * @return array
     */
    public function calculateWrapAmountItem($data, $item, $taxRate, $amount)
    {
        $multiplier = (int) $data[WrapItf::PRICE_TYPE] === Price::BY_QTY ? $item->getQty() : 1;
        $amount     += $data[WrapItf::AMOUNT] * $multiplier;

        $wrapAmount = $data[WrapItf::AMOUNT] * $multiplier;
        $wrapAmount *= (1 + $taxRate / 100);

        return [$amount, $wrapAmount];
    }

    /**
     * @param InvoiceCollection|CreditmemoCollection $collection
     * @param array $historyData
     *
     * @throws Exception
     */
    public function updateFromCollection($collection, &$historyData)
    {
        $this->wrapKeys = [];
        foreach ($collection as $item) {
            $this->updateWrapHistoryData($item, $historyData);
        }
    }

    /**
     * @param array $historyData
     *
     * @throws LocalizedException
     */
    public function updateHistoryTransaction($historyData)
    {
        if (!empty($historyData)) {
            $connection = $this->historyResource->getConnection();

            $connection->beginTransaction();
            try {
                foreach ($historyData as $historyId => $data) {
                    $connection->update(
                        $this->historyResource->getMainTable(),
                        $data,
                        ['history_id = ?' => $historyId]
                    );
                }
                $connection->commit();
            } catch (Exception $e) {
                $connection->rollBack();
                throw $e;
            }
        }
    }
}
