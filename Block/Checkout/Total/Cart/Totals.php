<?php
class Rack_Ketai_Block_Checkout_Cart_Totals extends Mage_Checkout_Block_Cart_Totals
{
    protected $_defaultRenderer = 'checkout/total_default';

    protected function _getTotalRenderer($code)
    {
        if (!isset($this->_totalRenderers[$code])) {
            $this->_totalRenderers[$code] = $this->_defaultRenderer;
            $config = Mage::getConfig()->getNode("global/sales/quote/totals/{$code}/renderer");
            if ($config)
                $this->_totalRenderers[$code] = (string) $config;

            $this->_totalRenderers[$code] = $this->getLayout()->createBlock($this->_totalRenderers[$code], "{$code}_total_renderer");
        }

        return $this->_totalRenderers[$code];
    }

}
