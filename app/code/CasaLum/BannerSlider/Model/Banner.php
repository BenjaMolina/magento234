<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\BannerSlider\Model;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

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
}