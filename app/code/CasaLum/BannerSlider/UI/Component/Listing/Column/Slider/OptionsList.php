<?php


namespace CasaLum\BannerSlider\Ui\Component\Listing\Column\Slider;

use Magento\Framework\Escaper;
use Magento\Framework\Data\OptionSourceInterface;
use CasaLum\BannerSlider\Model\SliderFactory as BannerSliderFactory;

/**
 * Class Options
 */
class OptionsList implements OptionSourceInterface
{
    /**
     * Escaper
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var BannerSliderFactory
     */
    protected $bannerSliderFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $currentOptions = [];

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
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options = $this->getAvailableGroups();

        return $this->options;
    }

    /**
     * Prepare groups
     *
     * @return array
     */
    private function getAvailableGroups()
    {
        $collection = $this->bannerSliderFactory->create()->getCollection();
        $result = [];
        $result[] = ['value' => ' ', 'label' => 'Select...'];
        foreach ($collection as $slider) {
            $result[] = ['value' => $slider->getId(), 'label' => $this->escaper->escapeHtml($slider->getName())];
        }
        return $result;
    }
}