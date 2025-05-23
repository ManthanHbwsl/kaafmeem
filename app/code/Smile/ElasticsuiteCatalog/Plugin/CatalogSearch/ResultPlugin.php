<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Smile ElasticSuite to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\ElasticsuiteCatalog
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2020 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */
namespace Smile\ElasticsuiteCatalog\Plugin\CatalogSearch;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Plugin which is responsible to redirect to a product page when only one result is found.
 *
 * @category Smile
 * @package  Smile\ElasticsuiteCatalog
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class ResultPlugin
{
    /**
     * Constant for configuration field location.
     */
    const REDIRECT_SETTINGS_CONFIG_XML_FLAG = 'smile_elasticsuite_catalogsearch_settings/catalogsearch/redirect_if_one_result';

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    private $helper;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * RedirectIfOneResult constructor.
     *
     * @param \Magento\Catalog\Model\Layer\Resolver              $layerResolver       Layer Resolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig         Scope Configuration
     * @param \Magento\CatalogSearch\Helper\Data                 $catalogSearchHelper Catalog Search Helper
     * @param \Magento\Framework\Message\ManagerInterface        $messageManager      Message Manager
     * @param \Magento\Framework\Controller\ResultFactory        $resultFactory       Result Interface Factory
     * @param \Magento\Framework\Event\ManagerInterface          $eventManager        Event Manager
     * @param \Magento\Framework\App\ResponseInterface           $response            Response
     */
    public function __construct(
        Resolver $layerResolver,
        ScopeConfigInterface $scopeConfig,
        Data $catalogSearchHelper,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        EventManagerInterface $eventManager,
        ResponseInterface $response
    ) {
        $this->layerResolver  = $layerResolver;
        $this->scopeConfig    = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->helper         = $catalogSearchHelper;
        $this->resultFactory  = $resultFactory;
        $this->eventManager   = $eventManager;
        $this->response       = $response;
    }

    /**
     * Process a redirect to the product page if there is only one result for a given search.
     *
     * @param \Magento\CatalogSearch\Controller\Result\Index $subject The CatalogSearch Result Controller
     * @param \Magento\Framework\Controller\ResultInterface  $result
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function afterExecute(
        \Magento\CatalogSearch\Controller\Result\Index $subject,
        $result
    ) {
        if (!$subject->getResponse()->isRedirect() &&
            $this->scopeConfig->isSetFlag(self::REDIRECT_SETTINGS_CONFIG_XML_FLAG, ScopeInterface::SCOPE_STORES)
        ) {
            $layer      = $this->layerResolver->get();
            $layerState = $layer->getState();

            if (count($layerState->getFilters()) === 0) {
                $productCollection = $layer->getProductCollection();
                if ($productCollection->getCurPage() === 1 && $productCollection->getSize() === 1) {
                    /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
                    $product = $productCollection->getFirstItem();
                    if ($product->getId()) {
                        $this->addRedirectMessage($product);
                        $result = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                        $result->setUrl($product->getProductUrl());
                        $this->response->setRedirect($product->getProductUrl());

                        $this->eventManager->dispatch(
                            'smile_elasticsuite_redirect_if_one_result',
                            [
                                'store_id'           => $product->getStoreId(),
                                'product_collection' => $productCollection,
                            ]
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Append message to the customer session to inform he has been redirected
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product The product being redirected to.
     */
    private function addRedirectMessage(ProductInterface $product)
    {
        $message = __("%1 is the only product matching your '%2' search.", $product->getName(), $this->helper->getEscapedQueryText());
        $this->messageManager->addSuccessMessage($message);
    }
}
