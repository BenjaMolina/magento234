<?php
namespace DCKAP\BestSellerProducts\Controller\Index;

use Magento\Framework\App\Action\Context;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Framework\App\Action\Action;

class Index extends Action {
    
    private $pageFactory;

    public  function __construct(PageFactory $pageFactory,Context $context){
        parent::__construct($context);
        $this->pageFactory=$pageFactory;
    }

    public function execute()
    {
        return $this->pageFactory->create();
    }
}