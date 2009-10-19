<?php
class Rack_Ketai_Block_Checkout_Cart_Sidebar extends Mage_Checkout_Block_Cart_Abstract
{
    const XML_PATH_CHECKOUT_SIDEBAR_COUNT   = 'checkout/sidebar/count';

    public function __construct()
    {
        parent::__construct();
        $this->addItemRender('default', 'checkout/cart_item_renderer', 'checkout/cart/sidebar/default.phtml');
    }

    /**
     * Get array last added items
     *
     * @return array
     */
    public function getRecentItems($count = null)
    {
        if ($count === null) {
            $count = $this->getData('item_count');
        }
        if ($count === null) {
            $count = Mage::getStoreConfig(self::XML_PATH_CHECKOUT_SIDEBAR_COUNT);
        }
        $items = array();
        if (!$this->getSummaryCount()) {
            return $items;
        }
        $i = 0;
        $allItems = array_reverse($this->getItems());
        foreach ($allItems as $item) {
            $items[] = $item;
            if (++$i == $count) {
                break;
            }
        }
        return $items;
    }

    /**
     * Get shopping cart subtotal.
     * It will include tax, if required by config settings.
     *
     * @return decimal
     */
    public function getSubtotal($skipTax = false)
    {
        $subtotal = 0;
        $totals = $this->getTotals();
        if (isset($totals['subtotal'])) {
            $subtotal = $totals['subtotal']->getValue();
            if (!$skipTax) {
                if ((!$this->helper('tax')->displayCartBothPrices()) && $this->helper('tax')->displayCartPriceInclTax()) {
                    $subtotal = $this->_addTax($subtotal);
                }
            }
        }
        return $subtotal;
    }

    /**
     * Get subtotal, including tax.
     * Will return > 0 only if appropriate config settings are enabled.
     *
     * @return decimal
     */
    public function getSubtotalInclTax()
    {
        if (!$this->helper('tax')->displayCartBothPrices()) {
            return 0;
        }
        return $this->_addTax($this->getSubtotal(true));
    }

    private function _addTax($price, $exclShippingTax=true) {
        $totals = $this->getTotals();
        if (isset($totals['tax'])) {
            if ($exclShippingTax) {
                $price += $totals['tax']->getValue()-$this->_getShippingTaxAmount();
            } else {
                $price += $totals['tax']->getValue();
            }
        }
        return $price;
    }

    protected function _getShippingTaxAmount()
    {
        return $this->getQuote()->getShippingAddress()->getShippingTaxAmount();
    }

    public function getSummaryCount()
    {
        return Mage::getSingleton('checkout/cart')->getSummaryQty();
    }

    public function getIncExcTax($flag)
    {
        $text = Mage::helper('tax')->getIncExcText($flag);
        return $text ? ' ('.$text.')' : '';
    }

    public function isPossibleOnepageCheckout()
    {
        return $this->helper('checkout')->canOnepageCheckout();
    }

    public function getCheckoutUrl()
    {
        return $this->helper('checkout/url')->getCheckoutUrl();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        if ((bool) Mage::app()->getStore()->getConfig('checkout/sidebar/display')) {
            $html = parent::_toHtml();
        }
        return $html;
    }
}
