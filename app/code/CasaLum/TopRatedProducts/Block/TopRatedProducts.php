<?php

namespace CasaLum\TopRatedProducts\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Catalog\Block\Product\AbstractProduct;

use \Magento\Catalog\Block\Product\Context;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\Catalog\Model\Product\Visibility;
use \Magento\Store\Model\StoreManagerInterface; 
use \Magento\Review\Model\ReviewFactory;
use \Magento\Framework\Registry;



class TopRatedProducts extends AbstractProduct {
    
    protected $_collectionFactory;
    protected $_catalogProductVisibility;
    protected $_storeManager;
    protected $_reviewFactory;
    protected $_registry;

    protected $_current_product;

    protected $_imageHelper;
	protected $_cartHelper;

    public function __construct(
        Context $context, 
        CollectionFactory $collectionFactory,
        Visibility $catalogProductVisibility,
        StoreManagerInterface $storeManager,
        ReviewFactory $reviewFactory,
        Registry $registry,
        array $data = []
    ){
        
        $this->_collectionFactory = $collectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager = $storeManager;
        $this->_reviewFactory = $reviewFactory;
        $this->_registry = $registry;

        $this->_imageHelper = $context->getImageHelper();
        $this->_cartHelper = $context->getCartHelper();
        
        parent::__construct($context, $data);
    }


    public function getProductCollection(){
        
        $limit = 10; 
        $storeId = $this->_storeManager->getStore()->getId();

        $productCollection = $this->_collectionFactory
        ->create()
        ->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
        ->addAttributeToSelect('*')
        ->joinField(
            'rating_summary', 
            'review_entity_summary', 
            'rating_summary', 
            'entity_pk_value=entity_id', 
            array(
                'entity_type'=>1, 
                'store_id'=> $storeId), 
            'left')
        ->addStoreFilter()
        ->setPageSize($limit)
        ;
        $productCollection->getSelect()->order('rating_summary DESC'); 
        //echo ($productCollection->getSelect());

        return $productCollection;
    }

    public function getRatingGlobal()
    {
        $storeId = $this->_storeManager->getStore()->getId();
	    $product = $this->getCurrentProduct();
	    $this->_reviewFactory->create()->getEntitySummary($product, $storeId);
	    $ratingGlobal = $product->getRatingSummary();

	    return $ratingGlobal;

    }

    public function getAllRatingsSummary()
    {
    	return $this->getRatingGlobal()->getRatingSummary();
    }

	public function getRatingCount()
	{
		return $this->getCurrentProduct()->getRatingSummary()->getReviewsCount();
	}

    public function getCurrentProduct()
    {
	    return $this->_current_product;
    }

    public function setCurrentProduct($product)
    {
	   $this->_current_product = $product;
    }
}