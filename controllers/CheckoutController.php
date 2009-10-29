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
        /*if (!preg_match('#^(login|register|saveMethod)#', $action)) {
            if (!Mage::getSingleton('customer/session')->authenticate($this, $this->_getHelper()->getMSLoginUrl())) {
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            }
        }*/

        if (!$this->_preDispatchValidateCustomer()) {
            return $this;
        }
/*
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
 */        return $this;
    }

    public function indexAction()
    {
        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
        $this->_getCheckout()->getCheckoutSession()->setCheckoutState(
            Mage_Checkout_Model_Session::CHECKOUT_STATE_BEGIN
        );
        $this->_redirect('*/*/billing');
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
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function billingAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($billingForm = $this->getLayout()->getBlock('customer_form_address')) {
            $billingForm->setCreateAccountUrl($this->_getHelper()->getMSRegisterUrl());
        }

        $this->renderLayout();
    }

    public function shippingmethodAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if($shippingForm = $this->getLayout()->getBlock('customer_form_address')) {
            $shippingForm->setCreateAccountUrl($this->_getHelper()->getMSRegisterUrl());
        }
        $this->renderLayout();
    }

    public function paymentmethodAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function overviewAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function successAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

    public function shippingPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result = $this->getKetai()->saveShipping($data, $customerAddressId);

            if (!isset($result['error'])) {
                $this->_redirect('*/*/shippingmethod');
            } elseif (isset($data['same_as_billing']) && ($data['same_as_billing'] == 1)){
                $this->_redirect('*/*/shippingmethod');
            } else {
                foreach($result['message'] as $_error => $_message) {
                    Mage::getSingleton('checkout/session')->addError($_message);
                }
                $this->_redirect('*/*/shipping');
            }
        } else {
            $this->_redirect('*/*/shipping/');
        }
    }

    public function billingPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            $result = $this->getKetai()->saveBilling($data, $customerAddressId);

            if (!isset($result['error'])) {
                /* check quote for virtual */
                if ($this->getKetai()->getQuote()->isVirtual()) {
                    $this->_redirect('*/*/paymentmethod');
                }
                elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
                    $this->_redirect('*/*/shippingmethod');
                }
                else {
                    $this->_redirect('*/*/shipping/');
                }
            } else {
                foreach($result['message'] as $_error => $_message) {
                    Mage::getSingleton('checkout/session')->addError($_message);
                }
                $this->_redirect('*/*/billing');
            }
        } else {
            $this->_redirect('*/*/billing');
        }
    }

    public function shippingmethodPostAction()
    {
         if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getKetai()->saveShippingMethod($data);
            
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method', array('request'=>$this->getRequest(), 'quote'=>$this->getKetai()->getQuote()));
                $this->_redirect('*/*/paymentmethod');
            } else {
                $this->_redirect('*/*/shippingmethod');
            }
         } else {
            $this->_redirect('*/*/shippingmethod/');
         }
    }

    public function paymentmethodPostAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());

            try {
                $result = $this->getKetai()->savePayment($data);
            }
            catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['error'] = $e->getMessage();
            }
            catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            
            if (empty($result['error'])) {
                $this->_redirect('*/*/overview');
            } else {
                $this->_redirect('*/*/paymentmethod');
            }
        } else {
            $this->_redirect('*/*/overview/');
        }
    }

    public function saveAction()
    {
        $result = array();
        try {
            if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                    $result['messages'][] = $this->__('Please agree to all Terms and Conditions before placing the order.');
                    foreach($result['message'] as $_error => $_message) {
                        Mage::getSingleton('checkout/session')->addError($_message);
                    }
                    return;
                }
            }
            if ($data = $this->getRequest()->getPost('payment', false)) {
                $this->getKetai()->getQuote()->getPayment()->importData($data);
            }
            $this->getKetai()->saveOrder();
            $this->_redirect('*/*/success');
        }
        catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getKetai()->getQuote(), $e->getMessage());
            $result['error_messages'] = $e->getMessage();

            if ($gotoSection = $this->getKetai()->getCheckout()->getGotoSection()) {
                $result['goto_section'] = $gotoSection;
                $this->getKetai()->getCheckout()->setGotoSection(null);
            }

            if ($updateSection = $this->getKetai()->getCheckout()->getUpdateSection()) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $result['update_section'] = array(
                        'name' => $updateSection,
                        'html' => $this->$updateSectionFunction()
                    );
                }
            }
            
            if(is_array($result['error_messages'])) {
                foreach($result['error_messages'] as $_error => $_message) {
                    Mage::getSingleton('checkout/session')->addError($_message);
                }
            } else {
                Mage::getSingleton('checkout/session')->addError($result['error_messages']);
            }
            
            $this->getKetai()->getQuote()->save();
            $this->_redirect('*/*/overview');
        }
        catch (Exception $e) {
            Mage::logException($e);
            Mage::helper('checkout')->sendPaymentFailedEmail($this->getKetai()->getQuote(), $e->getMessage());
            $this->_redirect('*/*/overview');
        }
    }

    public function getKetai()
    {
        return Mage::getSingleton('ketai/type_ketai');
    }

    public function saveMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('checkout_method');
            $result = $this->getKetai()->saveCheckoutMethod($method);
            $this->_redirect('*/*/billing');
        } else {
            $this0->_redirect('*/*/login');
        }
    }

}
