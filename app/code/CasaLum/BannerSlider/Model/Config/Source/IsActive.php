<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\BannerSlider\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class IsActive implements OptionSourceInterface
{
    /**
     * @var \CasaLum\BannerSlider\Model\Banner
     */
    protected $_banner;

    /**
     * Constructor
     *
     * @param \CasaLum\BannerSlider\Model\Banner $cmsPage
     */
    public function __construct(\CasaLum\BannerSlider\Model\Banner $banner)
    {
        $this->_banner = $banner;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->_banner->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
