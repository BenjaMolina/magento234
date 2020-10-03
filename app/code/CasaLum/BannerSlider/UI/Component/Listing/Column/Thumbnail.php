<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\BannerSlider\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use CasaLum\BannerSlider\Model\Banner\ImageUploader;

/**
 * Class Thumbnail
 *
 * @api
 * @since 100.0.2
 */
class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Url path
     */
    const URL_PATH_EDIT = 'casalum_banners_slider/banner/edit';

    /**
     * @var ImageUploader
     */
    protected $_imageUploader;

    /**
     * @var \PHPCuong\BannerSlider\Model\Banner
     */
    protected $banner;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \CasaLum\BannerSlider\Model\Banner $imageHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \CasaLum\BannerSlider\Model\Banner $banner,
        \Magento\Framework\UrlInterface $urlBuilder,
        ImageUploader $imageUploader,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->banner = $banner;
        $this->urlBuilder = $urlBuilder;
        $this->_imageUploader = $imageUploader;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $banner  = new \Magento\Framework\DataObject($item);
                $url = $banner['image'];
                $isOtherDirectoryImage = $this->_imageUploader->getIsOtherDirectoryImage($url);

                $item[$fieldName . '_src'] = $this->banner->getImageUrl($banner['image'], !$isOtherDirectoryImage);
                $item[$fieldName . '_orig_src'] = $this->banner->getImageUrl($banner['image'], !$isOtherDirectoryImage);
                //$item[$fieldName . '_alt'] = $this->getAlt($item) ?: $imageHelper->getLabel();
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    self::URL_PATH_EDIT,
                    ['banner_id' =>$banner['banner_id']]
                );
                $item[$fieldName . '_alt'] = $banner['name'];;
            }
        }

        return $dataSource;
    }
}
