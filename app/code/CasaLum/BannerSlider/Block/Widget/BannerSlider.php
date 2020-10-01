<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CasaLum\BannerSlider\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;


class BannerSlider extends Slider implements BlockInterface
{
    
    /**
     * @return array|AbstractCollection
     * @throws NoSuchEntityException
     */
    public function getBannersCollection()
    {
        $sliderId = (int)$this->getSliderId();

        $sliderCollection = $this->helperData->getActiveSliders();
        $slider           = $sliderCollection->addFieldToFilter('slider_id', $sliderId)->getFirstItem();
        $this->setSlider($slider);

        return parent::getBannerCollection();
    }

    public function getIdSlider() {
        return $this->getSliderId() || "";
    }
}