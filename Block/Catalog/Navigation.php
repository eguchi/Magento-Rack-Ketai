<?php
/**
 * Catalog navigation
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rack_Ketai_Block_Catalog_Navigation extends Mage_Catalog_Block_Navigation
{
    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int $level
     * @param boolean $last
     * @return string
     */
    public function drawItemSingle($category, $level=0, $last=false)
    {
        $html = '';
        if (!$category->getIsActive()) {
            return $html;
        }
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = $category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = $children && $childrenCount;
        $html.= '<li';
        if ($hasChildren) {
             $html.= ' onmouseover="toggleMenu(this,1)" onmouseout="toggleMenu(this,0)"';
        }

        $html.= ' class="level'.$level;
        $html.= ' nav-'.str_replace('/', '-', Mage::helper('catalog/category')->getCategoryUrlPath($category->getRequestPath()));
        if ($this->isCategoryActive($category)) {
            $html.= ' active';
        }
        if ($last) {
            $html .= ' last';
        }
        if ($hasChildren) {
            $cnt = 0;
            foreach ($children as $child) {
                if ($child->getIsActive()) {
                    $cnt++;
                }
            }
            if ($cnt > 0) {
                $html .= ' parent';
            }
        }
        $html.= '">'."\n";
        $html.= '<a href="'.$this->getCategoryUrl($category).'"><span>'.$this->htmlEscape($category->getName()).'</span></a>'."\n";

        $html.= '</li>'."\n";
        return $html;
    }
}
