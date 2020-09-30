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
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\RequestInterface;
use CasaLum\BannerSlider\Model\Slider as SliderModel;
use CasaLum\BannerSlider\Helper\Data as BannerSliderHelper;

/**
 * BannerSlider slider model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Slider extends AbstractDb
{
    
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
     * @var BannerSliderHelper
     */
    protected $bannerHelper;


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('casalum_bannerslider_slider', 'slider_id'); // Tabla -> ID
    }

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
        BannerSliderHelper $dataHelper
    ) {
        $this->date         = $date;
        $this->eventManager = $eventManager;
        $this->_request     = $request;
        $this->bannerHelper = $dataHelper;

        parent::__construct($context);
        $this->bannerSliderTable = $this->getTable('casalum_bannerslider_banner_slider'); 
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
        $slider = $object->getSliderId();
        $status = $object->getStatus();
        $prioridad = $object->getPrioridad();
        $desing = $object->getDesign();
        $autoPlayTimeout = $object->getautoplayTimeout();
        $margin = $object->getMargin();
        $storeIds = $object->getStoreIds();
        $responsiveItems = $object->getResponsiveItems();

        if(empty($name)){
            throw new LocalizedException(__("The banner name is required"));
        }
        if(empty($prioridad)){
            $object->setPrioridad(0);
        }
        if(empty($desing)){
            $object->setDesign(0);
        }

        if(!is_numeric($status) || empty($status)){
            throw new LocalizedException(__("The Status must be a numeric and is required"));
        }

        if(!empty($autoPlayTimeout) && !is_numeric($autoPlayTimeout) ){
            throw new LocalizedException(__("The Status must be a numeric"));
        }

        if(!empty($margin) && !is_numeric($margin) ){
            throw new LocalizedException(__("The Margin must be a numeric"));
        }

        if (is_array($storeIds)) {
            $object->setStoreIds(implode(',', $storeIds));
        }

        if ($responsiveItems && is_array($responsiveItems)) {
            $object->setResponsiveItems($this->bannerHelper->serialize($responsiveItems));
        } else {
            $object->setResponsiveItems(null);
        }

        //throw new LocalizedException(__("The banner name is required"));

        return parent::_beforeSave($object);

        return $this;
    }

     /**
     * @param AbstractModel $object
     *
     * @return $this|AbstractDb
     * @throws Zend_Serializer_Exception
     */
    protected function _afterLoad(AbstractModel $object)
    {
        parent::_afterLoad($object);
        $this->bannerHelper->unsetResponsiveItems($object);
        return $this;
    }

    /**
     * @param \CasaLum\BannerSlider\Model\Slider $slider
     *
     * @return array
     */
    public function getBannerRelationship(SliderModel $slider)
    {
        $select  = $this->getConnection()->select()->from(
                $this->bannerSliderTable,
                ['banner_id']
            )
            ->where('slider_id = ?', (int) $slider->getId());

        return $this->getConnection()->fetchCol($select);
    }
}