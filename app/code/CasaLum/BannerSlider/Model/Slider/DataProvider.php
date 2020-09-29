<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\BannerSlider\Model\Slider;

use CasaLum\BannerSlider\Model\ResourceModel\Slider\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Framework\App\ObjectManager;
use CasaLum\BannerSlider\Model\Banner\FileInfo;
use Magento\Framework\Filesystem;

/**
 * Class DataProvider
 */
class DataProvider extends \Magento\Ui\DataProvider\ModifierPoolDataProvider
{
    /**
     * @var \CasaLum\BannerSlider\Model\ResourceModel\Slider\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var Filesystem
     */
    private $fileInfo;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $sliderCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $sliderCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $sliderCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \CasaLum\BannerSlider\Model\Banner $banner */
        foreach ($items as $banner) {
            /*$banner = $this->convertValues($banner); //Obntenemos la imagen
            $banner = $this->setSliderRelationship($banner); //agregamos la relacion entre slider y banner*/
            $this->loadedData[$banner->getId()] = $banner->getData();
        }

        //Used from the save Action
        $data = $this->dataPersistor->get('banners_slider_slider');
        if (!empty($data)) {
            $banner = $this->collection->getNewEmptyItem();
            $banner->setData($data);
            $this->loadedData[$banner->getId()] = $banner->getData();
            $this->dataPersistor->clear('banners_slider_slider');
        }

        return $this->loadedData;
    }

     /**
     * Set slider_id and position to Banner
     *
     * @param \CasaLum\BannerSlider\Model\Banner $banner
     * @return \CasaLum\BannerSlider\Model\Banner $banner
     */
    private function setSliderRelationship($banner){
        $realtionship = $banner->getSlidersRelationship();
        foreach($realtionship as $value){
            $banner->addData($value);
        }

        return $banner;
    }
}
