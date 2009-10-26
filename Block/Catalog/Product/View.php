<?php
/**
 * Product View block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @module     Catalog
 */
class Rack_Ketai_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    public function getAddToCartUrl($product, $additional = array())
    {
        $additional = array();

        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        return $this->helper('ketai/cart')->getAddUrl($product, $additional);
    }

}
