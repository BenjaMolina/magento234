<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\BannerSlider\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use CasaLum\BannerSlider\Model\Banner\FileInfo;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * CMS block model
 *
 * @method Block setStoreId(int $storeId)
 * @method int getStoreId()
 */
class Banner extends AbstractModel 
{
    /**
     *Banner cache tag
     */
    const CACHE_TAG = 'casalum_bannerslider_banner'; //Nombre de la Tabla

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
        $this->_init(\CasaLum\BannerSlider\Model\ResourceModel\Banner::class);
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
     * Retrieve the Image URL
     *
     * @param string $imageName
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getImageUrl($imageName = null)
    {
        $url = '';
        $image = $imageName;
        if (!$image) {
            $image = $this->getData('image');
        }
        if ($image) {
            if (is_string($image)) {
                $url = $this->_getStoreManager()->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).FileInfo::ENTITY_MEDIA_PATH .'/'. $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

    /**
     * Get StoreManagerInterface instance
     *
     * @return StoreManagerInterface
     */
    private function _getStoreManager()
    {
        if ($this->_storeManager === null) {
            $this->_storeManager = ObjectManager::getInstance()->get(StoreManagerInterface::class);
        }
        return $this->_storeManager;
    }
}