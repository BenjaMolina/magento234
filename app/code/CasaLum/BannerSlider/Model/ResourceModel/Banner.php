<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\BannerSlider\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\AbstractModel;
use CasaLum\BannerSlider\Model\Banner as BannerModel;
use CasaLum\BannerSlider\Model\Banner\ImageUploader;

/**
 * CMS block model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Banner extends AbstractDb
{
    /**
     * @var ImageUploader
     */
    protected $_imageUploader;


    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;


    /**
     * Date model
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Slider relation model
     *
     * @var string
     */
    protected $bannerSliderTable;

    
    /**
     * constructor
     *
     * @param DateTime $date
     * @param ManagerInterface $eventManager
     * @param Context $context
     */
    public function __construct(
        DateTime $date,
        ManagerInterface $eventManager,
        Context $context,
        RequestInterface $request,
        ImageUploader $imageUploader
    ) {
        $this->date         = $date;
        $this->eventManager = $eventManager;
        $this->_request     = $request;
        $this->_imageUploader = $imageUploader;

        parent::__construct($context);
        $this->bannerSliderTable = $this->getTable('casalum_bannerslider_banner_slider'); 
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('casalum_bannerslider_banner', 'banner_id'); //Tabla -> ID
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _beforeSave(AbstractModel $object)
    {
          //set default Update At and Create At time post
          $object->setUpdatedAt($this->date->date());
          if ($object->isObjectNew()) {
              $object->setCreatedAt($this->date->date());
          }

          
        $name = $object->getName();
        $url = $object->getUrlBanner();
        $slider = $object->getSliderId();
        $order = $object->getPosition();
        $image = $object->getImage();

        if(empty($name)){
            throw new LocalizedException(__("The banner name is required"));
        }
        if(!empty($url) && !filter_var($url,FILTER_VALIDATE_URL)){
            throw new LocalizedException(__("The URL link is invalid"));
        }

        if(!empty( $image)){
            /**Lo que guardamos es el nombre de la imagen */
            if($this->_request->getParam('isAjax')) $object->setImage($image); //Para cuando es inlineEdit
            
            if(is_array($image)){
                $url = $image[0]['url'] ?: '';
                $isOtherDirectoryImage = $this->_imageUploader->getIsOtherDirectoryImage($url);
                
                if(!empty($url) && $isOtherDirectoryImage){
                    $url = str_replace('/pub/media/',"",$image[0]['url']);
                    $object->setImage($url);
                } 
                else $object->setImage($image[0]['name']);
            }

        }else{
            throw new LocalizedException(__("The image is required"));
        }

        if(!is_numeric($order)){
            throw new LocalizedException(__("The Sort Order must be a numeric"));
        }

        if(!is_numeric($slider)){ 
            throw new LocalizedException(__("The Slider is required"));
        }
        //throw new LocalizedException(__("The Slider is required"));
        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveSliderRelation($object);

        return parent::_afterSave($object);
    }


    /**
     * @param \CasaLum\BannerSlider\Model\Banner $slider
     *
     * @return array
     */
    public function getSlidersRelationship(BannerModel $banner)
    {
        $select  = $this->getConnection()->select()
            ->from(['table' => $this->bannerSliderTable], 
                    array(
                        'slider_id' => 'table.slider_id',
                        'position' => 'table.position'
                    )
            )
            ->where('banner_id = ?', (int) $banner->getId());

        return $this->getConnection()->fetchAll($select);

    }

     /**
     * @param \CasaLum\BannerSlider\Model\Banner $banner
     *
     * @return $this
     */
    protected function saveSliderRelation(BannerModel $banner)
    {
        $banner->setIsChangedSliderList(false);
        $id      = $banner->getId();
        $slider = $banner->getSliderId();
        if ($slider === null) {
            return $this;
        }
        $oldSliders = $banner->getSlidersRelationship();

        //throw new LocalizedException(__("The Slider is required"));
        $assocKeyIdSlider = array_column($oldSliders, 'slider_id', 'slider_id');
        $insert = (empty($oldSliders) && !in_array($slider, $assocKeyIdSlider)) ? true : false;
        $update = in_array($slider, $assocKeyIdSlider) ? true : false;
        $delete = (!empty($oldSliders) &&  !in_array($slider, $assocKeyIdSlider)) ? true : false;
        /*$insert  = array_diff($sliders, $oldSliders);
        $delete  = array_diff($oldSliders, $sliders);*/
        $adapter = $this->getConnection();

        if (!empty($delete)) {
            $condition = ['slider_id IN(?)' => $slider, 'banner_id=?' => $id];
            $adapter->delete($this->bannerSliderTable, $condition);
        }
        if (!empty($insert)) {
            $data[] = [
                'banner_id' => (int) $id,
                'slider_id' => (int) $slider,
                'position'  => $banner->getPosition()
            ];
            /*foreach ($insert as $tagId) {
                $data[] = [
                    'banner_id' => (int) $id,
                    'slider_id' => (int) $tagId,
                    'position'  => 1
                ];
            }*/
            $adapter->insertMultiple($this->bannerSliderTable, $data);
        }
        if(!empty($update)){
            $where = ['slider_id = ?' => (int) $slider, 'banner_id = ?' => (int) $id];
            $bind  = ['position' => (int) $banner->getPosition()];
            $adapter->update($this->bannerSliderTable, $bind, $where);
        }
        if (!empty($insert) || !empty($delete)) {
            /*$sliderIds = array_unique(array_merge(array_keys($insert), array_keys($delete)));
            $this->eventManager->dispatch(
                'mageplaza_bannerslider_banner_after_save_sliders',
                ['banner' => $banner, 'slider_ids' => $sliderIds]
            );*/

            $banner->setIsChangedSliderList(true);
            /*$sliderIds = array_keys($insert + $delete + $update);
            $banner->setAffectedSliderIds($sliderIds);*/
        }

        return $this;
    }

}