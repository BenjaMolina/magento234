<?php

namespace CasaLum\OneStepCheckout\Plugin\Checkout\Controller\Index;

class Index extends \Magento\Checkout\Controller\Index\Index
{

	public function aroundExecute(\Magento\Checkout\Controller\Index\Index $subject, \Closure $proceed)
    {
        $isEnableOneStepCheckout = $this->_objectManager->get('CasaLum\OneStepCheckout\Helper\Config')->isEnabledOneStep();

        if ($isEnableOneStepCheckout) {
            $checkoutHelper = $this->_objectManager->get('Magento\Checkout\Helper\Data');
            if (!$checkoutHelper->canOnepageCheckout()) {
                $this->messageManager->addErrorMessage(__('One-page checkout is turned off.'));
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }

            $quote = $this->getOnepage()->getQuote();
            if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }

            if (!$this->_customerSession->isLoggedIn() && !$checkoutHelper->isAllowedGuestCheckout($quote)) {
                $this->messageManager->addErrorMessage(__('Guest checkout is disabled.'));
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }

            // generate session ID only if connection is unsecure according to issues in session_regenerate_id function.
            // @see http://php.net/manual/en/function.session-regenerate-id.php
            if (!$this->isSecureRequest()) {
                $this->_customerSession->regenerateId();
            }

            $this->_objectManager->get('Magento\Checkout\Model\Session')->setCartWasUpdated(false);
            $this->getOnepage()->initCheckout();
            $resultPage = $this->resultPageFactory->create();
            //$resultPage->getLayout()->getUpdate()->addHandle('opcheckout_layout');
            $resultPage->getConfig()->getTitle()->set(__('Checkout'));
            return $resultPage;
            
        } else {
            $result = $proceed();
            return $result;
        }
    }

    /**
     * Checks if current request uses SSL and referer also is secure.
     *
     * @return bool
     */
    private function isSecureRequest(): bool
    {
        $request = $this->getRequest();

        $referrer = $request->getHeader('referer');
        $secure = false;

        if ($referrer) {
            $scheme = parse_url($referrer, PHP_URL_SCHEME);
            $secure = $scheme === 'https';
        }

        return $secure && $request->isSecure();
    }
}