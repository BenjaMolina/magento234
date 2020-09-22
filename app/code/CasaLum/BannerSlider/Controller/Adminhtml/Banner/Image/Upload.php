<?php
namespace CasaLum\BannerSlider\Controller\Adminhtml\Banner\Image;

use Magento\Backend\App\Action;
use Magento\Catalog\Model\ImageUploader;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Upload extends Action
{
    /**
     * @var CasaLum\BannerSlider\Model\ImageUploader
     */
    public $imageUploader;

    public function __construct(
        Context $context,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }

    public function execute()
    {
        $imageId = $this->getRequest()->getParam('param_name', 'image');
        try {
            $result = $this->imageUploader->saveFileToTmpDir($imageId);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CasaLum_BannerSlider::banner_create') || 
               $this->_authorization->isAllowed('CasaLum_BannerSlider::banner_view');
    }
}
