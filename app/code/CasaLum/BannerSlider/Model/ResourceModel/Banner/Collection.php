<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\BannerSlider\Model\ResourceModel\Banner;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection
 */
class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'banner_id';

    protected function _construct()
    {
        /* Enlazamos el Modelo con el ResourceModel */
        $this->_init(\CasaLum\BannerSlider\Model\Banner::class, \CasaLum\BannerSlider\Model\ResourceModel\Banner::class);
    }

}