<?php
class Rack_Ketai_Block_Checkout_Addresses extends Mage_Sales_Block_Items_Abstract
{
    public function getCheckout()
    {
        return Mage::getSingleton('ketai/type_checkout');
    }

    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('checkout')->__('Ship to Multiple Addresses') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    public function getItems()
    {
        $items = $this->getCheckout()->getQuoteShippingAddressesItems();
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }

    /**
     * Retrieve HTML for addresses dropdown
     *
     * @param  $item
     * @return string
     */
    public function getAddressesHtmlSelect($item, $index)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('ship['.$index.']['.$item->getQuoteItemId().'][address]')
            ->setId('ship_'.$index.'_'.$item->getQuoteItemId().'_address')
            ->setValue($item->getCustomerAddressId())
            ->setOptions($this->getAddressOptions());

        return $select->getHtml();
    }

    /**
     * Retrieve options for addresses dropdown
     *
     * @return array
     */
    public function getAddressOptions()
    {
        $options = $this->getData('address_options');
        if (is_null($options)) {
            $options = array();
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value'=>$address->getId(),
                    'label'=>$address->format('oneline')
                );
            }
            $this->setData('address_options', $options);
        }
        return $options;
    }

    public function getCustomer()
    {
        return $this->getCheckout()->getCustomerSession()->getCustomer();
    }

    public function getItemUrl($item)
    {
        return $this->getUrl('catalog/product/view/id/'.$item->getProductId());
    }

    public function getItemDeleteUrl($item)
    {
        return $this->getUrl('*/*/removeItem', array('address'=>$item->getQuoteAddressId(), 'id'=>$item->getId()));
    }

    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/addressesPost');
    }

    public function getNewAddressUrl()
    {
        return Mage::getUrl('*/checkout_address/newShipping');
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/cart/');
    }

    public function isContinueDisabled()
    {
        return !$this->getCheckout()->validateMinimumAmount();
    }
}
