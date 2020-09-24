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


/**
 * CMS block model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Banner extends AbstractDb
{
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
        RequestInterface $request
    ) {
        $this->date         = $date;
        $this->eventManager = $eventManager;
        $this->_request     = $request;

        parent::__construct($context);
        $this->bannerSliderTable = $this->getTable('mageplaza_bannerslider_banner_slider');
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
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
          //set default Update At and Create At time post
          $object->setUpdatedAt($this->date->date());
          if ($object->isObjectNew()) {
              $object->setCreatedAt($this->date->date());
          }

          
        $name = $object->getName();
        $url = $object->getUrlBanner();
        $slider = $object->getSliderId();
        $order = $object->getOrder() ?: $object->getPosition(); //(Position para inlineEdit)
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

            if(is_array($image)) $object->setImage($image[0]['name']);
        }else{
            throw new LocalizedException(__("The image is required"));
        }

        if(!is_numeric($order)){
            throw new LocalizedException(__("The Sort Order must be a numeric"));
        }

        if(!is_numeric($slider) && !$this->_request->getParam('isAjax')){ //Para cuando NO es inlineEdit
            throw new LocalizedException(__("The Slider is required"));
        }

        return $this;
    }

}