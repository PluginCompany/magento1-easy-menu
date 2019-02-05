<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Easymenu_Block_Page_Html_Topmenu extends Mage_Page_Block_Html_Topmenu {

    public function getHtml($outermostClass = '', $childrenWrapClass = '')
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $currentUrl = str_replace(array(Mage::getBaseUrl(),".html"),'',Mage::helper('core/url')->getCurrentUrl());
        $urlArray = explode("/", $currentUrl);
        $collection = Mage::getModel('easymenu/easymenu')->getMenuByStoreId($storeId);
        if (!$collection->count()) {
            $collection = Mage::getModel('easymenu/easymenu')->getMenuByStoreId(NULL);
        }
        if (!$collection->count()) {
            return parent::getHtml($outermostClass,$childrenWrapClass);
        }

        $html = '';
        $level = 0;
        $hierarchy = '';
        foreach ($collection as $item) {
            $nav = 'nav-';
            if ($item->getNav() != NULL) {
                $nav .= $item->getNav();
            }
            $nav .= $item->getItemIndex();


            $classes = '';
            $classes .= 'level' . $item->getLevel();
            if ($item->getHasChildren()) {
                $classes .= ' parent has-children';
            }
            $classes .= ' ' . $nav;

            if ($item->getItemIndex() == 1) {
                $classes .= ' first';
            }
            if ($item->getIsLast()) {
                $classes .= ' last';
            }

            $levelChange = $item->getLevel() - $level;
            $level = $item->getLevel();
            while($levelChange < 0){
                if (!empty($childrenWrapClass)) {
                    $html .= '</div>';
                }
                $html .= '</ul></li>';
                $levelChange++;
            }

            $aClass = '';
            if ($level == 0) {
                $classes .= ' ' . $outermostClass;

               $aClass = 'level0 ' . $outermostClass;
           }

            if($item->getHasChildren()){
                $aClass .= ' has-children ';
            }



            $suffix = '';
            if ((int)$item->getType() === 1) {
                $suffix     = Mage::getStoreConfig('catalog/seo/category_url_suffix', $storeId);
            }
            elseif ((int)$item->getType() === 4) {
                $suffix     = Mage::getStoreConfig('catalog/seo/product_url_suffix', $storeId);
            }

            if (!$item->getUseDeepLink()) {
                $itemUrl = $item->getLink() . $suffix;
            }else{
                $itemUrl = $item->getDeepLink() . $suffix;
            }

            $i = 0;
            $checkUrl = array();
            foreach ($urlArray as $part) {
                $checkUrl[] = $part;
                if ($i == $level) {
                    break;
                }
                $i++;
            }
            $checkUrl = implode("/", $checkUrl);
            if ($checkUrl == $item->getLink()) {
                $classes .= " active";
            }

            $html .= "<li class='$classes'>";

            $onclick = ' ';
            if (!$item->getLink()) {
                $onclick = ' onclick="return false;" ';
                $itemUrl = '#';
            }

            if(!stristr($itemUrl,'http://') && !stristr($itemUrl,'https://')){
                $itemUrl = $this->getBaseUrl() . $itemUrl;
            }

            $html .= '<a href="' .  $itemUrl . '" class="' . $aClass . '"' . $onclick . '><span>';
            $html .= $item->getTitle();
            $html .= "</span></a>";
            if ($item->getHasChildren()) {
                if (!empty($childrenWrapClass)) {
                    $html .= '<div class="' . $childrenWrapClass . '">';
                }
                $html .= '<ul>';
            }else{
                $html .= '</li>';
            }
        }
        return $html;
    }
}