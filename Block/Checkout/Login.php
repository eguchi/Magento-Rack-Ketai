<?php
class Rack_Ketai_Block_Checkout_Login extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        if (!$this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('login', array('label'=>Mage::helper('checkout')->__('Checkout method'), 'allow'=>true));
        }
        parent::_construct();
    }

    public function getMessages()
    {
        return Mage::getSingleton('customer/session')->getMessages(true);
    }

    public function getPostAction()
    {
        return Mage::getUrl('customer/account/loginPost', array('_secure'=>true));
    }

    public function getPostActionUrl()
    {
        return Mage::getUrl('ketai/checkout/saveMethod', array('_secure'=>true));
    }


    public function getMethod()
    {
        return $this->getQuote()->getMethod();
    }

    public function getMethodData()
    {
        return $this->getCheckout()->getMethodData();
    }

    public function getSuccessUrl()
    {
    	return $this->getUrl('*/*');
    }

    public function getErrorUrl()
    {
    	return $this->getUrl('*/*');
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        return Mage::getSingleton('customer/session')->getUsername(true);
    }
}
