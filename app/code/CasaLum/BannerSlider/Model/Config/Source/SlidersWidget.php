<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CasaLum\BannerSlider\Model\Config\Source;

use Magento\Framework\Escaper;
use CasaLum\BannerSlider\Model\SliderFactory as BannerSliderFactory;

class SlidersWidget implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var BannerSliderFactory
     */
    protected $bannerSliderFactory;

    /**
     * Escaper
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * Constructor
     *
     * @param BannerSliderFactory $systemStore
     * @param Escaper $escaper
     */
    public function __construct(BannerSliderFactory $bannerSliderFactory, Escaper $escaper)
    {
        $this->bannerSliderFactory = $bannerSliderFactory;
        $this->escaper = $escaper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAvailableSliders();
    }

    /**
     * Prepare Slider
     *
     * @return array
     */
    private function getAvailableSliders()
    {
        $collection = $this->bannerSliderFactory->create()->getCollection();
        $result = [];
        $result[] = ['value' => ' ', 'label' => 'Select...'];
        foreach ($collection as $group) {
            $result[] = ['value' => $group->getId(), 'label' => $this->escaper->escapeHtml($group->getName())];
        }
        return $result;
    }
}