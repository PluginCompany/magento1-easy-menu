<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Easymenu_Block_Adminhtml_Easymenubackend extends Mage_Adminhtml_Block_Template {

    protected $_storeId;
    protected $_disabledCategories;
    protected $_disabledCmsPages;

    protected function getStoreId()
    {
        if ($this->_storeId) {
            return $this->_storeId;
        }

        if ($storeId = Mage::app()->getRequest()->getParam('store')) {
            return $storeId;
        }
        else
        {
            $stores = Mage::getModel('core/store')->getCollection();
            $stores
                ->addFieldToFilter('website_id', array('neq' => 0));
            $store = $stores->getFirstItem();
            return $store->getId();
        }
    }

    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    public function getCategories()
    {
        $storeId = $this->getStoreId();

        $rootcatId= Mage::app()->getStore($storeId)->getRootCategoryId();
        $categories = Mage::getModel('catalog/category')->setStore($storeId)->getCategories($rootcatId);
        return $this->get_categories($categories);
    }

    public function get_categories($categories) {
        $result = '<ol>';
        foreach($categories as $category) {
            $result .= '<li class="menuitem scat" mtitle="' . $category->getName() . '" murl="' . $category->getUrlKey() . '" mtype="1" mid="' . $category->getId() . '" mrewrite="0" moriginaltitle="' . $category->getName() . '" usecustomtitle="0">';
            $result .= '<div><span class="disclose"><span></span></span><span class="title">' . $category->getName() . '</span></div>';
            if($category->hasChildren()) {
                $children = Mage::getModel('catalog/category')->getCategories($category->getId());
                $result .=  $this->get_categories($children);
            }
            $result .= '</li>';
        }
        return  $result . '</ol>';
     }


    public function getCurrentMenu()
    {
        $storeId = $this->getStoreId();
        $collection = Mage::getModel('easymenu/easymenu')->getMenuByStoreId($storeId);

        if(!$collection->count())
            return;

        $result = '<ol>';
        $currentLevel = 0;
        foreach ($collection as $item) {
            if($this->getItemTitleStoreId()){
                $item->setStoreId($this->getItemTitleStoreId());
            }
            $link = false;
            switch($item->getType()){
                case 1:
                    $class = 'scat';
                    break;
                case 2:
                    $class = 'scms';
                    break;
                case 3:
                    $class = 'link';
                    $link = $item->getLink();
                    break;
                case 4:
                    $class = 'product';
                    break;
            }

            if (!$link) {
                $link = explode('/', $item->getLink());
                $link = end($link);
            }

            $levelDifference = $currentLevel - $item->getLevel();
            if ($levelDifference < 0) {
                $levelDifference = 0;
            }
            while ($levelDifference > 1) {
                $levelDifference--;
                $result .= '</ol></li>';
            }
            $currentLevel = $item->getLevel();

            $result .= '<li class="menuitem ' . $class . '" mtitle="' . $item->getTitle() . '" murl="' . $link . '" mtype="' . $item->getType() . '" mid="' . $item->getTypeRelatedId() . '" mrewrite="' . $item->getUseDeepLink() .'" moriginaltitle="' . $item->getOriginalTitle() . '" usecustomtitle="' . $this->getCustomTitleText($item) . '">';
            $result .= '<div><span class="disclose"><span></span></span><span class="title">' . $item->getTitle() . '</span></div>';
            if ($item->getHasChildren()) {
                $result .= '<ol>';
            }elseif($item->getIsLast() && $item->getLevel()){
                $result .= '</li></ol></li>';
            }else{
                $result .= '</li>';
            }

        }
        while ($currentLevel > 1) {
            $result .= '</ol></li>';
            $currentLevel--;
        }

        return $result . '</ol>';
    }

    public function getCategoriesJSON()
    {
        $storeId = $this->getStoreId();

        if (!$storeId) {
            $storeId = 1;
        }
        $categories = Mage::getModel('catalog/category')->getCollection();

        $categories
            ->addAttributeToSelect('*')
            ->setStoreId($storeId)
            ->setStore(Mage::app()->getStore($storeId))
            ->load();
        return json_encode($categories->toArray());
    }
    
    public function getCustomTitleText($item)
    {
        if($item->getUseCustomTitle()){
            return "1";
        }
        return "0";
    }
}