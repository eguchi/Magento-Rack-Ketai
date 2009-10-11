<?php
class Rack_Ketai_Block_Checkout_Abstract extends Mage_Core_Block_Template
{
    public function getCheckout()
    {
        return Mage::getSingleton('ketai/type_checkout');
    }
}
