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

/**
 * BannerSlider slider model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Slider extends AbstractDb
{

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