<?php
class Rack_Ketai_Block_Checkout_Link extends Mage_Checkout_Block_Onepage_Link
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

    public function addCartLink()
    {
        if ($parentBlock = $this->getParentBlock()) {
            $count = $this->helper('checkout/cart')->getSummaryCount();

            if( $count == 1 ) {
                $text = $this->__('My Cart (%s item)', $count);
            } elseif( $count > 0 ) {
                $text = $this->__('My Cart (%s items)', $count);
            } else {
                $text = $this->__('My Cart');
            }

            $parentBlock->addLink($text, 'ketai/cart', $text, true, array(), 50, null, 'class="top-link-cart"');
        }
        return $this;
    }

    public function addCheckoutLink()
    {
        if (!$this->helper('checkout')->canOnepageCheckout()) {
            return $this;
        }
        if ($parentBlock = $this->getParentBlock()) {
            $text = $this->__('Checkout');
            $parentBlock->addLink($text, 'ketai/checkout', $text, true, array(), 60, null, 'class="top-link-checkout"');
        }
        return $this;
    }
}
