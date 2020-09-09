<?php

namespace CasaLum\TopRatedProducts\Block;

use \Magento\Framework\View\Element\Template;
use \Magento\Catalog\Block\Product\AbstractProduct;

use \Magento\Catalog\Block\Product\Context;
use \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use \Magento\Catalog\Model\Product\Visibility;
use \Magento\Store\Model\StoreManagerInterface; 
use \Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewSummary;



class TopRatedProducts extends AbstractProduct {
    
    protected $_collectionFactory;
    protected $_catalogProductVisibility;
    protected $_storeManager;
    protected $_reviewSummary;

    protected $_imageHelper;
	protected $_cartHelper;

    public function __construct(
        Context $context, 
        CollectionFactory $collectionFactory,
        Visibility $catalogProductVisibility,
        StoreManagerInterface $storeManager,
        ReviewSummary $reviewSummary,
        array $data = []
    ){
        
        $this->_collectionFactory = $collectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager = $storeManager;
        $this->_reviewSummary = $reviewSummary;

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
}