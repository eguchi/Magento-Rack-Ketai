<?php
class Rack_Ketai_Block_Catalog_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable
{
    public function getOptionsList()
    {
        $parent = $this->getParentBlock();
        $options = Zend_Json::decode($parent->getChild('options_configurable')->getJsonConfig());
        var_dump($options);
    }

}
