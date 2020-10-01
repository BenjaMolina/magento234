<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CasaLum\BannerSlider\Helper;

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\AbstractModel;
use CasaLum\BannerSlider\Model\BannerFactory;
use CasaLum\BannerSlider\Model\Config\Source\Effect;
use CasaLum\BannerSlider\Model\ResourceModel\Banner\Collection;
use CasaLum\BannerSlider\Model\Slider;
use CasaLum\BannerSlider\Model\SliderFactory;
use CasaLum\BannerSlider\Helper\AbstractData;



/**
 * Class Data
 * @package Mageplaza\BannerSlider\Helper
 */
class Data extends AbstractData
{
    const CONFIG_MODULE_PATH = 'csbannerslider';

    /**
     * @var BannerFactory
     */
    public $bannerFactory;

    /**
     * @var SliderFactory
     */
    public $sliderFactory;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * Data constructor.
     *
     * @param DateTime $date
     * @param Context $context
     * @param HttpContext $httpContext
     * @param BannerFactory $bannerFactory
     * @param SliderFactory $sliderFactory
     * @param StoreManagerInterface $storeManager
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        DateTime $date,
        Context $context,
        HttpContext $httpContext,
        BannerFactory $bannerFactory,
        SliderFactory $sliderFactory,
        StoreManagerInterface $storeManager,
        ObjectManagerInterface $objectManager
    ) {
        $this->date          = $date;
        $this->httpContext   = $httpContext;
        $this->bannerFactory = $bannerFactory;
        $this->sliderFactory = $sliderFactory;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param Slider $slider
     *
     * @return false|string
     */
    public function getBannerOptions($slider)
    {
        $config = $slider->getData();

        $defaultOpt    = $this->getDefaultConfig($config);
        $responsiveOpt = $this->getResponsiveConfig($slider);
        $effectOpt     = $this->getEffectConfig($slider);

        $sliderOptions = array_merge($defaultOpt, $responsiveOpt, $effectOpt);

        return self::jsonEncode($sliderOptions);
    }

    /**
     * @param array $configs
     *
     * @return array
     */
    public function getDefaultConfig($configs)
    {
        $basicConfig = [];
        foreach ($configs as $key => $value) {
            if (in_array(
                $key,
                ['autoWidth', 'autoHeight', 'loop', 'nav', 'dots', 'lazyLoad', 'autoplay', 'autoplayTimeout','margin']
            )) {
                $basicConfig[$key] = (int) $value;
            }
        }

        return $basicConfig;
    }

    /**
     * @param null $slider
     *
     * @return array
     */
    public function getResponsiveConfig($slider = null)
    {
        $sliderResponsive  = $slider->getIsResponsive();

        if(!$sliderResponsive) return ['items' => 1];

        $responsiveItemsValue = $slider->getResponsiveItems();

        try {
            $responsiveItems = $this->unserialize($responsiveItemsValue);
        } catch (Exception $e) {
            $responsiveItems = [];
        }

        $result = [];
        foreach ($responsiveItems as $config) {
            $size          = $config['size'] ?: 0;
            $items         = $config['items'] ?: 0;
            $result[$size] = ['items' => $items];
        }

        return ['responsive' => $result];
    }

    /**
     * @param $slider
     *
     * @return array
     */
    public function getEffectConfig($slider)
    {
        if (!$slider) {
            return [];
        }

        if ($slider->getEffect() === Effect::SLIDER) {
            return [];
        }

        return ['animateOut' => $slider->getEffect()];
    }

    /**
     * @param null $id
     *
     * @return Collection
     */
    public function getBannerCollection($id = null)
    {
        $collection = $this->bannerFactory->create()->getCollection();

        $collection->join(
            ['banner_slider' => $collection->getTable('casalum_bannerslider_banner_slider')],
            'main_table.banner_id=banner_slider.banner_id AND banner_slider.slider_id=' . $id,
            ['position']
        );

        $collection->addOrder('position', 'ASC');

        return $collection;
    }

    /**
     * @return \Mageplaza\BannerSlider\Model\ResourceModel\Slider\Collection
     * @throws NoSuchEntityException
     */
    public function getActiveSliders()
    {
        /** @var \Mageplaza\BannerSlider\Model\ResourceModel\Slider\Collection $collection */
        $collection = $this->sliderFactory->create()
            ->getCollection()
            ->addFieldToFilter('status', 1)
            ->addOrder('priority');

        return $collection;
    }

    public function unsetResponsiveItems(AbstractModel $slider)
    {
       if (!empty($slider->getResponsiveItems()) && is_string($slider->getResponsiveItems())) {
            $slider->setResponsiveItems($this->unserialize($slider->getResponsiveItems()));
        } else if(!is_array($slider->getResponsiveItems())){
            $slider->unsResponsiveItems();
        }

        return $slider;

    }

}
