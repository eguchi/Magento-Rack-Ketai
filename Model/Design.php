<?php
class Rack_Ketai_Model_Design extends Mage_Core_Model_Design_Package
{
    
    public function setTheme()
    {
        $error = error_reporting(E_ALL);
        $include_path = get_include_path();
        set_include_path($include_path . PS . BP . DS . 'lib/PEAR');

        require_once('Net/UserAgent/Mobile.php');
        $agent = Net_UserAgent_Mobile::singleton();

        switch( true )
        {
            case ($agent->isDoCoMo()):
            case ($agent->isVodafone()):
            case ($agent->isEZweb()):
                error_reporting($error);
                foreach (array('layout', 'template', 'skin', 'locale') as $type) {
    				$this->_theme[$type] = "ketai";
    			}
    			return $this;
                break;

            default:
                error_reporting($error);
                switch (func_num_args()) {
	             case 1:
    		            foreach (array('layout', 'template', 'skin', 'locale') as $type) {
    			        $this->_theme[$type] = func_get_arg(0);
    		            }
    			    break;
	             case 2:
			    $this->_theme[func_get_arg(0)] = func_get_arg(1);
			    break;
	             default:
	                    throw Mage::exception(Mage::helper('core')->__('Wrong number of arguments for %s', __METHOD__));
		}
                break;
         }	
    }
}
