<?php
class Rack_Ketai_Helper_Url extends Mage_Core_Helper_Url
{
    /**
     * Retrieve shopping cart url
     *
     * @return string
     */
    public function getCartUrl()
    {
        return $this->_getUrl('checkout/cart');
    }

    /**
     * Retrieve checkout url
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->_getUrl('ketai/checkout');
    }

    /**
     * Multi Shipping (MS) checkout urls
     */

    /**
     * Retrieve multishipping checkout url
     *
     * @return string
     */
    public function getMSCheckoutUrl()
    {
        return $this->_getUrl('ketai/checkout');
    }

    public function getMSLoginUrl()
    {
        return $this->_getUrl('ketai/checkout/login', array('_secure'=>true, '_current'=>true));
    }

    public function getMSAddressesUrl()
    {
        return $this->_getUrl('ketai/checkout/addresses');
    }

    public function getMSShippingAddressSavedUrl()
    {
        return $this->_getUrl('ketai/checkout/shippingSaved');
    }

    public function getMSRegisterUrl()
    {
        return $this->_getUrl('ketai/checkout/register');
    }

    /**
     * One Page (OP) checkout urls
     */
    public function getOPCheckoutUrl()
    {
        return $this->_getUrl('ketai/checkout');
    }
}
