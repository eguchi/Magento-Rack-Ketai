<?php
var_dump("aho");
/**
 * Product options block
 *
 * @category   Rack
 * @package    Rack_Ketai
 * @author     Magento-JP User Group <rack990@gmail.com>
 */
class Rack_Ketai_Catalog_Block_Product_View_Options extends Mage_Catalog_Block_Product_View_Options
{
    /**
     * Get option render by given type
     *
     * @param string $type
     * @return array
     */
    public function getOptionRender($type)
    {
        if (isset($this->_optionRenders[$type])) {
            return $this->_optionRenders[$type];
        }

        return $this->_optionRenders['default'];
    }

    public function getGroupOfOption($type)
    {
        $group = Mage::getSingleton('catalog/product_option')->getGroupByType($type);

        return $group == '' ? 'default' : $group;
    }

    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getProduct()->getOptions();
    }

    public function hasOptions()
    {
        if ($this->getOptions()) {
            return true;
        }
        return false;
    }

    public function getJsonConfig()
    {
        $config = array();

        foreach ($this->getOptions() as $option) {
            /* @var $option Mage_Catalog_Model_Product_Option */
            $priceValue = 0;
            if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                $_tmpPriceValues = array();
                foreach ($option->getValues() as $value) {
                    /* @var $value Mage_Catalog_Model_Product_Option_Value */
            	   $_tmpPriceValues[$value->getId()] = Mage::helper('core')->currency($value->getPrice(true), false, false);
                }
                $priceValue = $_tmpPriceValues;
            } else {
                $priceValue = Mage::helper('core')->currency($option->getPrice(), false, false);
            }
            $config[$option->getId()] = $priceValue;
        }

        return Zend_Json::encode($config);
    }

    /**
     * Get option html block
     *
     * @param Mage_Catalog_Model_Product_Option $option
     */
    public function getOptionHtml(Mage_Catalog_Model_Product_Option $option)
    {
        $renderer = $this->getOptionRender(
            $this->getGroupOfOption($option->getType())
        );
        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
                ->setTemplate($renderer['template']);
        }
        return $renderer['renderer']
            ->setProduct($this->getProduct())
            ->setOption($option)
            ->toHtml();
    }
}
