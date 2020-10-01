<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CasaLum\BannerSlider\Block\Widget;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use CasaLum\BannerSlider\Helper\Data as HelperData;


class Slider extends Template
{
      /**
     * @var \CasaLum\BannerSlider\Helper\Data
     */
    protected $helperData;

    /**
     * @type StoreManagerInterface
     */
    protected $store;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var DateTime
     */
    protected $_date;

    /**
     * @var FilterProvider
     */
    public $filterProvider;

    /**
     * Slider constructor.
     *
     * @param Template\Context $context
     * @param HelperData $helperData
     * @param CustomerRepositoryInterface $customerRepository
     * @param DateTime $dateTime
     * @param FilterProvider $filterProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        CustomerRepositoryInterface $customerRepository,
        DateTime $dateTime,
        FilterProvider $filterProvider,
        array $data = []
    ) {
        $this->helperData         = $helperData;
        $this->customerRepository = $customerRepository;
        $this->store              = $context->getStoreManager();
        $this->_date              = $dateTime;
        $this->filterProvider     = $filterProvider;

        parent::__construct($context, $data);
    }

    /**
     * @return array|AbstractCollection
     */
    public function getBannerCollection()
    {
        $collection = [];
        $sliderId = $this->getSliderId();
        if ($sliderId) {
            $collection = $this->helperData->getBannerCollection($sliderId)->addFieldToFilter('status', 1);
        }

        return $collection;
    }

    /**
     * @return false|string
     */
    public function getBannerOptions()
    {
        $slider = $this->getSlider();
        return $this->helperData->getBannerOptions($slider);
    }
}