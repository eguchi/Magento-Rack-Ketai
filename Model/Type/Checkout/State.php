<?php
class Rack_Ketai_Model_Type_Checkout_State extends Varien_Object
{
    const STEP_SHIPPING         = 'checkout_shipping';
    const STEP_BILLING          = 'checkout_billing';
    const STEP_OVERVIEW         = 'checkout_overview';
    const STEP_SUCCESS          = 'checkout_success';
    const STEP_SHIPPINGMETHOD   = 'checkout_shippingmethod';
    const STEP_PAYMENTMETHOD    = 'checkout_paymentmethod';

    /**
     * Allow steps array
     *
     * @var array
     */
    protected $_steps;

    protected $_checkout;

    /**
     * Init model, steps
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->_steps = array(
            self::STEP_SHIPPING => new Varien_Object(array(
                'label' => Mage::helper('checkout')->__('Shipping Information')
            )),
            self::STEP_BILLING => new Varien_Object(array(
                'label' => Mage::helper('checkout')->__('Billing Information')
            )),
            self::STEP_OVERVIEW => new Varien_Object(array(
                'label' => Mage::helper('checkout')->__('Place Order')
            )),
            self::STEP_SUCCESS => new Varien_Object(array(
                'label' => Mage::helper('checkout')->__('Order Success')
            )),
        );

        foreach ($this->_steps as $step) {
            $step->setIsComplete(false);
        }

        $this->_checkout = Mage::getSingleton('ketai/type_checkout');
        $this->_steps[$this->getActiveStep()]->setIsActive(true);
    }

    public function getCheckout()
    {
        return $this->_checkout;
    }

    public function getSteps()
    {
        return $this->_steps;
    }

    public function getActiveStep()
    {
        $step = $this->getCheckoutSession()->getCheckoutState();
        if (isset($this->_steps[$step])) {
            return $step;
        }
        return self::STEP_SHIPPING;
    }

    public function setActiveStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setCheckoutState($step);
        }
        else {
            $this->getCheckoutSession()->setCheckoutState(self::STEP_SHIPPING);
        }

        // Fix active step changing
        if(!$this->_steps[$step]->getIsActive()) {
            foreach($this->getSteps() as $stepObject) {
                $stepObject->unsIsActive();
            }
            $this->_steps[$step]->setIsActive(true);
        }
        return $this;
    }

    /**
     * Mark step as completed
     *
     * @param string $step
     * @return Mage_Checkout_Model_Type_Multishipping_State
     */
    public function setCompleteStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setStepData($step, 'is_complete', true);
        }
        return $this;
    }

    /**
     * Retrieve step complete status
     *
     * @param string $step
     * @return bool
     */
    public function getCompleteStep($step)
    {
        if (isset($this->_steps[$step])) {
            return $this->getCheckoutSession()->getStepData($step, 'is_complete');
        }
        return false;
    }

    /**
     * Unset complete status from step
     *
     * @param string $step
     * @return Mage_Checkout_Model_Type_Multishipping_State
     */
    public function unsCompleteStep($step)
    {
        if (isset($this->_steps[$step])) {
            $this->getCheckoutSession()->setStepData($step, 'is_complete', false);
        }
        return $this;
    }

    public function canSelectAddresses()
    {

    }

    public function canInputShipping()
    {

    }

    public function canSeeOverview()
    {

    }

    public function canSuccess()
    {

    }

    /**
     * Retrieve checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}
