<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CasaLum\BannerSlider\Controller\Adminhtml\Slider;

use Magento\Framework\App\Action\Action;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('slider_id');
        $model = $this->_objectManager->create(\CasaLum\BannerSlider\Model\Slider::class);

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This banner no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('banners_slider_slider', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create()
                        ->setActiveMenu('CasaLum_BannerSlider::all_sliders')
                        ->addBreadcrumb(__('Banners Sliders'), __('Banners Sliders'))
                        ->addBreadcrumb(__('All Sliders'), __('All Sliders'))
                        ->addBreadcrumb(
                            $id ? __('Edit Slider') : __('New Slider'),
                            $id ? __('Edit Slider') : __('New Slider')
                        );
        $resultPage->getConfig()->getTitle()->prepend(__('All Sliders'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getName() : __('New Sldier'));
        return $resultPage;
    }


     /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CasaLum_BannerSlider::slider_create') || 
               $this->_authorization->isAllowed('CasaLum_BannerSlider::slider_view');
    }

}

