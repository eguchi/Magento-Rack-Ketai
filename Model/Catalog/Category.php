<?php

class Kdl_Ketai_Model_Catalog_Category extends Mage_Catalog_Model_Category
{


    /**
     * Get category products collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function getProductCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId($this->getStoreId())
            ->addCategoryFilter($this);
        return $collection;
    }


    /**
     * Retrieve Layout Update Handle name
     *
     * @return string
     */
    public function getLayoutUpdateHandle()
    {
        $layout = 'catalog_category_';
        if ($this->getIsAnchor()) {
            $layout .= 'layered';
        }
        else {
            $layout .= 'default';
        }
        return $layout;
    }

    /**
     * Retrieve URL path
     *
     * @return string
     */
    public function getUrlPath()
    {
        if ($path = $this->getData('url_path')) {
            return $path;
        }

        $path = $this->getUrlKey();

        if ($this->getParentId()) {
            $parentPath = Mage::getModel('catalog/category')->load($this->getParentId())->getCategoryPath();
            $path = $parentPath.'/'.$path;
        }

        $this->setUrlPath($path);

        return $path;
    }

    /**
     * Retrieve design attributes array
     *
     * @return array
     */
    public function getDesignAttributes()
    {
        $result = array();
        foreach ($this->_designAttributes as $attrName) {
            $result[] = $this->_getAttribute($attrName);
        }
        return $result;
    }

    /**
     * Retrieve attribute by code
     *
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    private function _getAttribute($attributeCode)
    {
        if (!$this->_useFlatResource) {
            $attribute = $this->getResource()->getAttribute($attributeCode);
        }
        else {
            $attribute = Mage::getSingleton('catalog/config')
                ->getAttribute('catalog_category', $attributeCode);
        }
        return $attribute;
    }

    /**
     * Get array categories ids which are part of category path
     * Result array contain id of current category because it is part of the path
     *
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve level
     *
     * @return int
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    /**
     * Verify category ids
     *
     * @param array $ids
     * @return bool
     */
    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    /**
     * Retrieve Is Category has children flag
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * Retrieve Request Path
     *
     * @return string
     */
    public function getRequestPath()
    {
        return $this->_getData('request_path');
    }

    /**
     * Retrieve Name data wraper
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * Before delete process
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException("Can't delete root category.");
        }
        return parent::_beforeDelete();
    }

    /**
     * Retrieve anchors above
     *
     * @return array
     */
    public function getAnchorsAbove()
    {
        $anchors = array();
        $path = $this->getPathIds();

        if (in_array($this->getId(), $path)) {
            unset($path[array_search($this->getId(), $path)]);
        }

        if ($this->_useFlatResource) {
            $anchors = $this->_getResource()->getAnchorsAbove($path, $this->getStoreId());
        }
        else {
            if (!Mage::registry('_category_is_anchor_attribute')) {
                $model = $this->_getAttribute('is_anchor');
                Mage::register('_category_is_anchor_attribute', $model);
            }

            if ($isAnchorAttribute = Mage::registry('_category_is_anchor_attribute')) {
                $anchors = $this->getResource()->findWhereAttributeIs($path, $isAnchorAttribute, 1);
            }
        }
        return $anchors;
    }

    /**
     * Retrieve count products of category
     *
     * @return int
     */
    public function getProductCount()
    {
        if (!$this->hasProductCount()) {
            $count = $this->_getResource()->getProductCount($this); // load product count
            $this->setData('product_count', $count);
        }
        return $this->getData('product_count');
    }

    /**
     * Retrieve categories by parent
     *
     * @param int $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return mixed
     */
    public function getCategories($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
    {
        $categories = $this->getResource()
            ->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
        return $categories;
    }

    /**
     * Return parent categories of current category
     *
     * @return array
     */
    public function getParentCategories()
    {
        return $this->getResource()->getParentCategories($this);
    }

    /**
     * Retuen children categories of current category
     *
     * @return array
     */
    public function getChildrenCategories()
    {
        return $this->getResource()->getChildrenCategories($this);
    }

    /**
     * Check category is in Root Category list
     *
     * @return bool
     */
    public function isInRootCategoryList()
    {
        return $this->getResource()->isInRootCategoryList($this);
    }

    /**
     * Retrieve Available int Product Listing sort by
     *
     * @return null|array
     */
    public function getAvailableSortBy()
    {
        $available = $this->getData('available_sort_by');
        if (empty($available)) {
            return array();
        }
        if ($available && !is_array($available)) {
            $available = split(',', $available);
        }
        return $available;
    }

    /**
     * Retrieve Available Product Listing  Sort By
     * code as key, value - name
     *
     * @return array
     */
    public function getAvailableSortByOptions() {
        $availableSortBy = array();
        $defaultSortBy   = Mage::getSingleton('catalog/config')
            ->getAttributeUsedForSortByArray();
        if ($this->getAvailableSortBy()) {
            foreach ($this->getAvailableSortBy() as $sortBy) {
                if (isset($defaultSortBy[$sortBy])) {
                    $availableSortBy[$sortBy] = $defaultSortBy[$sortBy];
                }
            }
        }

        if (!$availableSortBy) {
            $availableSortBy = $defaultSortBy;
        }

        return $availableSortBy;
    }

}
