<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Fbuilder\Block\Products;

/**
 * Main contact form block
 */
class SpecialProduct extends \Magento\Catalog\Block\Product\AbstractProduct
{
	/**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
	
	
	protected $_count;
	
	/**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;
	
	/**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;
	
    protected $_resource;
    
    // protected $_template = 'MGS_Fbuilder::products/single/deals.phtml';
	
	/**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;
	
    protected $categoryRepository;  

    protected $_product;

	
	
    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
		\Magento\Framework\Url\Helper\Data $urlHelper,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ProductFactory $productMagento,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
		$this->urlHelper = $urlHelper;
		$this->_resource = $resource;
		$this->productRepository = $productRepository;
		$this->categoryRepository = $categoryRepository;
        $this->formKey = $formKey;
        $this->_product = $productMagento;
        parent::__construct(
            $context,
            $data
        );
    }
	
	public function getLoadCategory($id)
    {
        return $this->_categoryloader->create()->load($id);
    }
	
	public function getProduct(){
        $product = $this->_product->create();
        $idProduct = $this->getData('product_id');

		if(isset($idProduct)){
            $data = $product->load($idProduct);
            if($data->getId()){
                $productResult =  $this->productRepository->getById($this->getData('product_id'));
            }else {
                $productResult = [];
            }

            return $productResult;
          
        }
        return false;
        
	}
	
	public function getCategoriesLink($_product){
		$categories = $_product->getCategoryIds();
		$html = '';
		if(count($categories)>0){
			foreach($categories as $_categoryId){
				$category = $this->categoryRepository->get($_categoryId);
				$html .= '<a href="'.$category->getUrl().'" class="category-link">'.$category->getName().'</a>, ';
			}
			$html = substr($html, 0, -2);
		}
		return $html;
	}
	
	public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
            'action' => $url,
            'data' => [
                'product' => $product->getEntityId(),
                \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                    $this->urlHelper->getEncodedUrl($url),
            ]
        ];
    }
	
	/**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}

