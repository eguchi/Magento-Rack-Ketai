<?php
class Rack_Ketai_CheckoutController extends Mage_Checkout_Controller_Action
{
     /*
     * Retrieve checkout model
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('ketai/type_checkout');
    }

    /**
     * Retrieve checkout state model
     *
     * @return Mage_Checkout_Model_Type_Multishipping_State
     */
    protected function _getState()
    {
        return Mage::getSingleton('ketai/type_checkout_state');
    }

    /**
     * Retrieve checkout url heler
     *
     * @return Mage_Checkout_Helper_Url
     */
    protected function _getHelper()
    {
        return Mage::helper('ketai/url');
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     *
     * @return Mage_Checkout_MultishippingController
     */
    public function preDispatch()
    {
        parent::preDispatch();
        
        $action = $this->getRequest()->getActionName();
        if (!preg_match('#^(login|register)#', $action)) {
            if (!Mage::getSingleton('customer/session')->authenticate($this, $this->_getHelper()->getMSLoginUrl())) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            }

            if (!Mage::helper('checkout')->isMultishippingCheckoutAvailable()) {
                $this->_redirectUrl($this->_getHelper()->getCartUrl());
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
                return $this;
            }
        }

        if (!$this->_preDispatchValidateCustomer()) {
            return $this;
        }

        if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
            && !in_array($action, array('index', 'login', 'register', 'success'))) {
            $this->_redirectUrl($this->_getHelper()->getCartUrl());
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }

        if ($action == 'success' && $this->_getCheckout()->getCheckoutSession()->getDisplaySuccess(true)) {
            return $this;
        }

        $quote = $this->_getCheckout()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError() || $quote->isVirtual()) {
            $this->_redirectUrl($this->_getHelper()->getCartUrl());
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
        return $this;
    }

    public function indexAction()
    {
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        $this->_getCheckout()->getCheckoutSession()->setCheckoutState(
            Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN
        );
        $this->_redirect('*/*/shipping');
    }

    /**
     * Multishipping checkout login page
     */
    public function loginAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        
        // set account create url
        if ($loginForm = $this->getLayout()->getBlock('customer_form_login')) {
            $loginForm->setCreateAccountUrl($this->_getHelper()->getMSRegisterUrl());
        }
        $this->renderLayout();
    }

    public function registerAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirectUrl($this->_getHelper()->getMSCheckoutUrl());
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        if ($registerForm = $this->getLayout()->getBlock('customer_form_register')) {
            $registerForm->setShowAddressFields(true)
                ->setBackUrl($this->_getHelper()->getMSLoginUrl())
                ->setSuccessUrl($this->_getHelper()->getMSShippingAddressSavedUrl())
                ->setErrorUrl($this->_getHelper()->getCurrentUrl());
        }

        $this->renderLayout();
    }

    public function shippingAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function billingAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function shippingmethodAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function paymentmethodAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function overviewAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function successAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function shippingPostAction()
    {
        $this->_redirect('*/*/billing/');
    }

    public function billingPostAction()
    {
        $this->_redirect('*/*/shippingmethod/');
    }

    public function shippingmethodPostAction()
    {
        $this->_redirect('*/*/paymentmethod/');
    }

    public function paymentmethodPostAction()
    {
        $this->_redirect('*/*/overview/');
    }

    public function saveAction()
    {
        $this->_redirect('*/*/success');
    }
}
