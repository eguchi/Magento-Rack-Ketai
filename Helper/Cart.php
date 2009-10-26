<?php
class Rack_Ketai_Helper_Cart extends Mage_Checkout_Helper_Cart
{
    public function getAddUrl($product, $additional = array())
    {
		$continueShoppingUrl = $this->getCurrentUrl();

        $params = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core')->urlEncode($continueShoppingUrl),
            'product' => $product->getId()
        );

        if ($this->_getRequest()->getRouteName() == 'checkout'
            && $this->_getRequest()->getControllerName() == 'cart') {
            $params['in_cart'] = 1;
        }

        if (count($additional)){
            $params = array_merge($params, $additional);
        }

        return $this->_getUrl('ketai/cart/add', $params);
    }

}
