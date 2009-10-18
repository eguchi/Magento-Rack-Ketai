<?php
class Rack_Ketai_Block_Checkout_Shipping extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping', array(
            'label'     => Mage::helper('checkout')->__('Shipping Information'),
            'is_show'   => $this->isShow()
        ));

        parent::_construct();
    }

    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    public function getAddress()
    {
        if ($this->isCustomerLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            return $customer->getPrimaryShippingAddress();
        } else {
            return Mage::getModel('sales/quote_address');
        }
    }

    public function getAddresses()
    {
        return $this->getCheckout()->getQuote()->getAllShippingAddresses();
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow()
    {
        return !$this->getQuote()->isVirtual();
    }

    public function addItemRender($test)
    {
        var_dump($test);
    }

    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/shippingPost');
    }

}
