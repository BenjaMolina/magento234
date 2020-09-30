<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CasaLum\BannerSlider\Controller\Adminhtml\Slider;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use CasaLum\BannerSlider\Model\Slider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;


class Save extends Action
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CasaLum_BannerSlider::slider_create') || 
               $this->_authorization->isAllowed('CasaLum_BannerSlider::slider_update');
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (isset($data['status']) && $data['status'] === 'true') {
                $data['status'] = Slider::STATUS_ENABLED;
            }
            if (empty($data['slider_id'])) {
                $data['slider_id'] = null;
            }

            /** @var \CasaLum\BannerSlider\Model\Banner $model */
            $model = $this->_objectManager->create(Slider::class);
            //$model = $this->blockFactory->create();

            $id = $this->getRequest()->getParam('slider_id');
            if ($id) {
                try {
                    $model = $this->_objectManager->create(Slider::class)->load($id);
                    //$model = $this->blockRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This banner no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                /*Antes de gaurdar va al _beoforeSave() del Model\ResourceModel\Slider */
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the banner.'));
                $this->dataPersistor->clear('banners_slider');//Igual que en CasaLum\BannerSlider\Model\Banner\DataProvider
                return $this->processBlockReturn($model, $data, $resultRedirect);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the banner.'));
            }

            $this->dataPersistor->set('banners_slider_slider', $data); //Igual que en CasaLum\BannerSlider\Model\Banner\DataProvider
            return $resultRedirect->setPath('*/*/edit', ['slider_id' => $id]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process and set the block return
     *
     * @param \Magento\Cms\Model\Block $model
     * @param array $data
     * @param \Magento\Framework\Controller\ResultInterface $resultRedirect
     * @return \Magento\Framework\Controller\ResultInterface
     */
    private function processBlockReturn($model, $data, $resultRedirect)
    {
        $redirect = $this->getRequest()->getParam('back');
        //$redirect = $data['back'] ?? 'close';

        if ($redirect ==='edit') {
            $resultRedirect->setPath('*/*/edit', ['slider_id' => $model->getId()]);
        } else if ($redirect === 'duplicate') {
            //$duplicateModel = $this->blockFactory->create(['data' => $data]);
            $duplicateModel = $this->_objectManager->create(\CasaLum\BannerSlider\Model\Banner::class);
            $duplicateModel->setData($data);
            $duplicateModel->setId(null);
            $duplicateModel->setIdentifier($data['identifier'] . '-' . uniqid());
            $duplicateModel->setIsActive(Banner::STATUS_DISABLED);
            $model->save();
            //$this->blockRepository->save($duplicateModel);
            $id = $duplicateModel->getId();
            $this->messageManager->addSuccessMessage(__('You duplicated the block.'));
            $this->dataPersistor->set('banners_slider', $data);
            $resultRedirect->setPath('*/*/edit', ['slider_id' => $id]);
        }
        else{
            $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect;
    }

}