<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\BannerSlider\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * BannerSlider slider model
 *
 */
class Slider extends AbstractModel 
{
    /**
     *Slider cache tag
     */
    const CACHE_TAG = 'casalum_bannerslider_slider'; //Nombre de la Tabla

    /**#@+
     * Banner's statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**#@-*/

    /**#@-*/
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = self::CACHE_TAG;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\CasaLum\BannerSlider\Model\ResourceModel\Slider::class);
    }

    /**
     * Prepare Banner's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * @return array|mixed
     */
    public function getBannerRelationship()
    {
        if (!$this->getId()) {
            return [];
        }

        $array = $this->getData('banners_relationship');
        if ($array === null) {
            $array = $this->getResource()->getBannerRelationship($this);
            $this->setData('banners_relationship', $array);
        }

        return $array;
    }
}