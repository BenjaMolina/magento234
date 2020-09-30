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
use CasaLum\BannerSlider\Model\BannerFactory;
use CasaLum\BannerSlider\Model\Config\Source\Effect;
use CasaLum\BannerSlider\Model\ResourceModel\Banner\Collection;
use CasaLum\BannerSlider\Model\Slider;
use CasaLum\BannerSlider\Model\SliderFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Data
 * @package Mageplaza\BannerSlider\Helper
 */
class Data extends AbstractHelper
{
    const CONFIG_MODULE_PATH = 'csbannerslider';

     /**
     * @type ObjectManagerInterface
     */
    protected $objectManager;

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
        $this->objectManager = $objectManager;

        parent::__construct($context);
    }

    /**
     * @param Slider $slider
     *
     * @return false|string
     */
    public function getBannerOptions($slider)
    {
        if ($slider && $slider->getDesign() === '1') { //not use Config
            $config = $slider->getData();
        } else {
            $config = $this->getModuleConfig('mpbannerslider_design');
        }

        $defaultOpt    = $this->getDefaultConfig($config);
        $defaultOpt['margin'] = 10; //Esto esta harcodeado
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
                ['autoWidth', 'autoHeight', 'loop', 'nav', 'dots', 'lazyLoad', 'autoplay', 'autoplayTimeout']
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
        $defaultResponsive = $this->getModuleConfig('mpbannerslider_design/responsive');
        $sliderResponsive  = $slider->getIsResponsive();

        if ((!$defaultResponsive && !$sliderResponsive) || (!$sliderResponsive && $slider->getDesign())) {
            return ['items' => 1];
        }

        $responsiveItemsValue = $slider->getDesign()
            ? $slider->getResponsiveItems()
            : $this->getModuleConfig('mpbannerslider_design/item_slider');

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
            ['banner_slider' => $collection->getTable('mageplaza_bannerslider_banner_slider')],
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
            ->addFieldToFilter('customer_group_ids', [
                'finset' => $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP)
            ])
            ->addFieldToFilter('status', 1)
            ->addOrder('priority');

        $collection->getSelect()
            ->where('FIND_IN_SET(0, store_ids) OR FIND_IN_SET(?, store_ids)', $this->storeManager->getStore()->getId())
            ->where('from_date is null OR from_date <= ?', $this->date->date())
            ->where('to_date is null OR to_date >= ?', $this->date->date());

        return $collection;
    }


    /**
     * @param $ver
     * @param string $operator
     *
     * @return mixed
     */
    public function versionCompare($ver, $operator = '>=')
    {
        $productMetadata = $this->objectManager->get(ProductMetadataInterface::class);
        $version = $productMetadata->getVersion(); //will return the magento version

        return version_compare($version, $ver, $operator);
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function serialize($data)
    {
        if ($this->versionCompare('2.2.0')) {
            return self::jsonEncode($data);
        }

        return $this->getSerializeClass()->serialize($data);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public function unserialize($string)
    {
        if ($this->versionCompare('2.2.0')) {
            return self::jsonDecode($string);
        }

        return $this->getSerializeClass()->unserialize($string);
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     *
     * @return string
     */
    public static function jsonEncode($valueToEncode)
    {
        try {
            $encodeValue = self::getJsonHelper()->jsonEncode($valueToEncode);
        } catch (Exception $e) {
            $encodeValue = '{}';
        }

        return $encodeValue;
    }

    /**
     * Decodes the given $encodedValue string which is
     * encoded in the JSON format
     *
     * @param string $encodedValue
     *
     * @return mixed
     */
    public static function jsonDecode($encodedValue)
    {
        try {
            $decodeValue = self::getJsonHelper()->jsonDecode($encodedValue);
        } catch (Exception $e) {
            $decodeValue = [];
        }

        return $decodeValue;
    }

    /**
     * @return JsonHelper|mixed
     */
    public static function getJsonHelper()
    {
        return ObjectManager::getInstance()->get(JsonHelper::class);
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
