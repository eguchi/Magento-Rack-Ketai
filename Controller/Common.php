<?php
class Kdl_Ketai_Controller_Common extends Mage_Core_Controller_Front_Action
{
    public function renderLayout($output='')
    {
        $_profilerKey = self::PROFILER_KEY . '::' . $this->getFullActionName();

        if ($this->getFlag('', 'no-renderLayout')) {
            return;
        }

        if (Mage::app()->getFrontController()->getNoRender()) {
            return;
        }

        Varien_Profiler::start("$_profilerKey::layout_render");


        if (''!==$output) {
            $this->getLayout()->addOutputBlock($output);
        }

        Mage::dispatchEvent('controller_action_layout_render_before');
        Mage::dispatchEvent('controller_action_layout_render_before_'.$this->getFullActionName());

        #ob_implicit_flush();
        $this->getLayout()->setDirectOutput(false);

        $output = $this->getLayout()->getOutput();

        //$this->getResponse()->setHeader("Content-Type", "text/html; charset=Shift_JIS", true);
        $this->getResponse()->setHeader("Content-Type", "text/html; charset=UTF-8", true);
        //$this->getResponse()->appendBody(mb_convert_encoding($output, "SJIS", "UTF-8"));
        $this->getResponse()->appendBody($output);
        
        Varien_Profiler::stop("$_profilerKey::layout_render");

        return $this;
    }
    
}
?>
