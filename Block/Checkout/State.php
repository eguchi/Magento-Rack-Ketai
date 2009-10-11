<?php
class Rack_Ketai_Block_Checkout_State extends Mage_Core_Block_Template
{
    public function getSteps()
    {
        return Mage::getSingleton('ketai/type_checkout_state')->getSteps();
    }
}
