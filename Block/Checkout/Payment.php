<?php
class Rack_Ketai_Block_Checkout_Onepage_Payment extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('payment', array(
            'label'     => $this->__('Payment Information'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }
}
