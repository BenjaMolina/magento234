<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CasaLum\BannerSlider\Controller\Adminhtml\Slider;

use Magento\Framework\App\Action\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class InlineEdit extends Action
{

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $sliderId) {
                    /** @var \CasaLum\BannerSlider\Model\Slider $model */
                    $model = $this->_objectManager->create(\CasaLum\BannerSlider\Model\Slider::class);
                    $model->load($sliderId);
                    try {
                        $model->setData(array_merge($model->getData(), $postItems[$sliderId]));
                        $model->save();
                    } catch (\Exception $e) {
                        $messages[] = $this->getErrorWithSliderId(
                            $model,
                            __($e->getMessage())
                        );
                        $error = true;
                    }
                }
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Slider name to error message
     *
     * @param \CasaLum\BannerSlider\Model\Slider $slider
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithSliderId(\CasaLum\BannerSlider\Model\Sldier $slider, $errorText)
    {
        return '[Slider ID: ' . $slider->getId() . '] ' . $errorText;
    }


     /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CasaLum_BannerSlider::slider_create') || 
               $this->_authorization->isAllowed('CasaLum_BannerSlider::slider_update');
    }

}

