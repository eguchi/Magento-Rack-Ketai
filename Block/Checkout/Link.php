<?php
class Rack_Ketai_Block_Checkout_Link extends Mage_Core_Block_Template
{
    public function getCheckoutUrl()
    {
        return $this->getUrl('ketai/checkout', array('_secure'=>true));
    }

    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function _toHtml()
    {
        if (!Mage::helper('checkout')->isMultishippingCheckoutAvailable()){
            return '';
        }

        return parent::_toHtml();
    }
}
