<?php

class Kdl_Ketai_Checkout_IndexController extends Kdl_Ketai_Controller_Common
{
    function indexAction()
    {
        $this->_redirect('checkout/onepage', array('_secure'=>true));
    }
}