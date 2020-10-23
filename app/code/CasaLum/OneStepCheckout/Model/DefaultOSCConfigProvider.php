<?php

namespace CasaLum\OneStepCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DefaultOSCConfigProvider implements ConfigProviderInterface
{
    protected $_configHelper;
	
    protected $_oscHelper;
	
    public function __construct(
        \CasaLum\OneStepCheckout\Helper\Config $configHelper,
        \CasaLum\OneStepCheckout\Helper\Data $oscHelper
    ) {
        $this->_configHelper = $configHelper;
        $this->_oscHelper = $oscHelper;
    }

    public function getConfig()
    {
        $output['checkout_description'] = $this->_configHelper->getOneStepCheckout('general/checkout_description');
        $output['checkout_title'] = $this->_configHelper->getOneStepCheckout('general/checkout_title');
        $output['show_login_link'] = (boolean) $this->_configHelper->getOneStepCheckout('general/show_login_link');
        $output['is_login'] = (boolean) $this->_configHelper->isLogin();
        $output['login_link_title'] = $this->_configHelper->getOneStepCheckout('general/login_link_title');
        $output['show_discount'] = (boolean) $this->_configHelper->getOneStepCheckout('general/show_discount');
        $output['terms_enable'] = (boolean) $this->_configHelper->canTermsAndConditions();
        $output['terms_and_con_title'] = $this->_configHelper->getTermsAndConTitle();
        $output['terms_and_con_terms_content'] = $this->_configHelper->getTermsAndConTermsContent();
        $output['terms_and_con_warning'] = $this->_configHelper->getTermsAndConWarning();
        $output['terms_and_con_warning_content'] = $this->_configHelper->getTermsAndConWarningContent();
        $output['show_shipping_address'] = (boolean) $this->_configHelper->getOneStepCheckout('general/show_shipping_address');
        return $output;
    }
    
    
}
