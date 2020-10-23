<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\OneStepCheckout\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Class LayoutProcessor
 */
class LayoutProcessor implements LayoutProcessorInterface
{
    protected $_helperConfig;
   
    public function __construct(
        \CasaLum\OneStepCheckout\Helper\Config $helperConfig
    ) {
        $this->_helperConfig = $helperConfig;
    }

    public function process($jsLayout)
    {
        if ($this->_helperConfig->isEnabledOneStep()) {
            if(isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount'])) {
                unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['discount']);
            }
           
            if(isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']['before-place-order']['children']['agreements'])) {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children']['before-place-order']['children']['agreements']['config']['template'] = "CasaLum_OneStepCheckout/checkout/checkout-agreements";
            }
        }
        return $jsLayout;
    }

}
