<?php

namespace CasaLum\OneStepCheckout\Helper;

use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper {

    protected $_customerSession;
	
    protected $_regionCollection;
	
    protected $_directoryHelper;

    protected $_subscriberFactory;

    protected $_moduleManager;

    protected $_localCountry;

    protected $_objectManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Directory\Model\ResourceModel\Region\Collection $regionCollection,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Config\Model\Config\Source\Locale\Country $localCountry,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_customerSession = $customerSession;
        $this->_regionCollection = $regionCollection;
        $this->_directoryHelper = $directoryHelper;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_moduleManager = $context->getModuleManager();
        $this->_localCountry = $localCountry;
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * ID Section etc\adminhtml\system.xml
     */
    const SECTION_CONFIG_ONESTEPCHECKOUT = 'onestepcheckout';
    
    public function getOneStepCheckout($relativePath) {
        return $this->scopeConfig->getValue(self::SECTION_CONFIG_ONESTEPCHECKOUT . '/' . $relativePath);
    }

    public function isEnabledOneStep() {
        return $this->getOneStepCheckout('general/active');
    }
	
    public function getFullRequest()
    {
        $routeName = $this->_getRequest()->getRouteName();
        $controllerName = $this->_getRequest()->getControllerName();
        $actionName = $this->_getRequest()->getActionName();
        return $routeName.'_'.$controllerName.'_'.$actionName;
    }

    public function isLogin() {
        return $this->_customerSession->isLoggedIn();
    }

    public function isAllowCountries($countryCode) {
        $allowCountries = explode(',', (string)$this->scopeConfig->getValue('general/country/allow',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        if (!empty($allowCountries)) {
            if (!in_array($countryCode, $allowCountries)) {
                return false;
            }
        }
        return true;
    }
    
    public function getMinimumPasswordLength()
    {
        return $this->scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    public function getRequiredCharacterClassesNumber()
    {
        return $this->scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }

	public function canTermsAndConditions()
    {
        $terms_and_conditions = $this->getOneStepCheckout('terms_and_conditions/terms_enable');

        if ($terms_and_conditions) {
            return true;
        } else {
            return false;
        }
    }
	
	public function getTermsAndConWarning()
    {
        return $this->getOneStepCheckout('terms_and_conditions/terms_warning');
    }
	
	public function getTermsAndConWarningContent()
    {
        return $this->getOneStepCheckout('terms_and_conditions/terms_warning_content');
    }
	
	public function getTermsAndConTermsContent()
    {
        return $this->getOneStepCheckout('terms_and_conditions/terms_content');
    }
	
	public function getTermsAndConTitle()
    {
        return $this->getOneStepCheckout('terms_and_conditions/terms_text');
    }

    public function getMagentoVersion() {
        $productMetadata = $this->_objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        return $productMetadata->getVersion();
    }

    public function canShowPasswordMeterValidate() {
        if(version_compare($this->getMagentoVersion(), '2.1.0') >= 0) {
            return true;
        } else {
            return false;
        }
    }
}