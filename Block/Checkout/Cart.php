<?php
class Rack_Ketai_Block_Cart extends Mage_Checkout_Block_Cart_Abstract
{

    public function chooseTemplate()
    {
        if ($this->getQuote()->getItemsCount()) {
            $this->setTemplate($this->getCartTemplate());
        } else {
            $this->setTemplate($this->getEmptyTemplate());
        }
    }

    public function hasError()
    {
        return $this->getQuote()->getHasError();
    }

    public function getItemsSummaryQty()
    {
        return $this->getQuote()->getItemsSummaryQty();
    }

    public function isWishlistActive()
    {
        $isActive = $this->_getData('is_wishlist_active');
        if ($isActive === null) {
            $isActive = Mage::getStoreConfig('wishlist/general/active') && Mage::getSingleton('customer/session')->isLoggedIn();
            $this->setIsWishlistActive($isActive);
        }
        return $isActive;
    }

    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', array('_secure'=>true));
    }

    public function getContinueShoppingUrl()
    {
        $url = $this->getData('continue_shopping_url');
        if (is_null($url)) {
            $url = Mage::getSingleton('checkout/session')->getContinueShoppingUrl(true);
            if (!$url) {
                $url = Mage::getUrl();
            }
            $this->setData('continue_shopping_url', $url);
        }
        return $url;
    }

    public function getIsVirtual()
    {
        return $this->helper('checkout/cart')->getIsVirtualQuote();
    }

}
