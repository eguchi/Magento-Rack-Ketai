<?php
class Rack_Ketai_Block_Checkout_Billing extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('billing', array(
            'label'     => Mage::helper('checkout')->__('Billing Information'),
            'is_show'   => $this->isShow()
        ));

        if ($this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('billing', 'allow', true);
        }
        parent::_construct();
    }

    public function isUseBillingAddressForShipping()
    {
        if (($this->getQuote()->getIsVirtual())
            || !$this->getQuote()->getShippingAddress()->getSameAsBilling()) {
            return false;
        }
        return true;
    }

    public function getCountries()
    {
        return Mage::getResourceModel('directory/country_collection')->loadByStore();
    }

    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    function getAddress() {
        if (!$this->isCustomerLoggedIn()) {
            return $this->getQuote()->getBillingAddress();
        } else {
            return Mage::getModel('sales/quote_address');
        }
    }

    public function getFirstname()
    {
        $firstname = $this->getAddress()->getFirstname();
        if (empty($firstname) && $this->getQuote()->getCustomer()) {
            return $this->getQuote()->getCustomer()->getFirstname();
        }
        return $firstname;
    }

    public function getLastname()
    {
        $lastname = $this->getAddress()->getLastname();
        if (empty($lastname) && $this->getQuote()->getCustomer()) {
            return $this->getQuote()->getCustomer()->getLastname();
        }
        return $lastname;
    }

    public function canShip()
    {
        return !$this->getQuote()->isVirtual();
    }

    public function getSaveUrl()
    {
        return $this->getUrl("*/*/billingPost");
    }

    public function getAddressesHtmlRadio($type)
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->format('oneline')
                );
            }

            $addressId = $this->getAddress()->getId();
            if (empty($addressId)) {
                if ($type=='billing') {
                    $address = $this->getCustomer()->getPrimaryBillingAddress();
                } else {
                    $address = $this->getCustomer()->getPrimaryShippingAddress();
                }
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('ketai/checkout_html_radio')
                ->setName($type.'_address_id')
                ->setId($type.'-address-raido')
                ->setClass('address-raido')
                ->setDefaultValue($addressId)
                ->setOptions($options);

            //$select->addOption('', Mage::helper('checkout')->__('New Address'));

            return $select->getHtml();
        }
        return '';
    }

}
