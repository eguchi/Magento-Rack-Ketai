<?php


class Kdl_Ketai_Model_Page extends Mage_Core_Model_Abstract
{

    const NOROUTE_PAGE_ID = 'no-route';

    protected $_eventPrefix = 'kdl_ketai_page';

    protected function _construct()
    {
        $this->_init('cms/page');
    }

    public function load($id, $field=null)
    {
        if (is_null($id)) {
            return $this->noRoutePage();
        }
        return parent::load($id, $field);
    }

    public function noRoutePage()
    {
        $this->setData($this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName()));
        return $this;
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param   string $identifier
     * @param   int $storeId
     * @return  int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }
}
