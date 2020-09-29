<?php


namespace CasaLum\BannerSlider\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\Data\OptionSourceInterface;
use CasaLum\BannerSlider\Model\BannerFactory as BannersFactory;

/**
 * Class Options
 */
class BannerList implements OptionSourceInterface
{
    /**
     * Escaper
     *
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var BannersFactory
     */
    protected $bannerFactory;

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
     * @param BannersFactory $systemStore
     * @param Escaper $escaper
     */
    public function __construct(BannersFactory $bannerFactory, Escaper $escaper)
    {
        $this->bannerFactory = $bannerFactory;
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
        $collection = $this->bannerFactory->create()->getCollection();
        $result = [];
        $result[] = ['value' => ' ', 'label' => 'Select...'];
        foreach ($collection as $slider) {
            $result[] = ['value' => $slider->getId(), 'label' => $this->escaper->escapeHtml($slider->getName())];
        }
        return $result;
    }
}