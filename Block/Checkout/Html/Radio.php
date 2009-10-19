<?php
class Rack_Ketai_Block_Checkout_Html_Radio extends Mage_Core_Block_Abstract
{

    protected $defaultValue = 0;

    public function setId($id)
    {
        $this->setData('id', $id);
        return $this;
    }

    public function setClass($class)
    {
        $this->setData('class', $class);
        return $this;
    }

    public function setTitle($title)
    {
        $this->setData('title', $title);
        return $this;
    }

    public function setDefauleValue($value)
    {
        $this->defaultValue = $value;
        return $this;
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function getClass()
    {
        return $this->getData('class');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }
        
        $html ="";
        foreach ($this->getOptions() as $key => $option) {
            $value = "";
            $label = "";
            if (is_array($option)){
                $value = $option['value'];
                $label = $option['label'];
            } else {            
                $value = $key;
                $label = $option;
            }
            $html = '<input type="radio" name="'.$this->getName().'" id="'.$this->getId().'" class="'
                .$this->getClass().'" title="'.$this->getTitle().'" '.$this->getExtraParams()
                .'value="'.$value.'" />' . $label . '<br/>';
        }
        return $html;
    }

    public function getHtml()
    {
        return $this->toHtml();
    }

}
