<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Easymenu_Model_Easymenu extends Mage_Core_Model_Abstract
{
    protected $_originalTitle;
    
    protected function _construct(){
       $this->_init("easymenu/easymenu");
    }

    public function deleteByStoreId($storeId)
    {
        if (!$storeId) {
            $collection = $this->getCollection()->addFieldToFilter('store_id',array('null' => true));
        }else{
            $collection = $this->getCollection()->addFieldToFilter('store_id',$storeId);
        }
        foreach ($collection as $item) {
            $item->delete();
        }
        return $this;
    }

    public function getMenuByStoreId($storeId)
    {
        $collection = $this->getCollection()->addFieldToFilter('store_id',$storeId);
        $collection->setOrder('drawnumber','asc');
        return $collection;
    }

    public function createUrlRewrite($item)
    {
        $storeId = $item['store_id'];
        $idPath = 'plugincompanyeasymenu/' . $item['type'] . '/' . $item['type_related_id'];

        $categoryId = NULL;
        $productId = NULL;
        $suffix = "";
        if ($item['type'] == 1) {
            //category
            $suffix     = Mage::getStoreConfig('catalog/seo/category_url_suffix', $storeId);
            $categoryId = $item['type_related_id'];
            $targetPath = "catalog/category/view/id/" . $categoryId;
        }elseif ($item['type'] == 2) {
            //cms page
            $targetPath = end(explode("/", $item['deep_link']));
        }elseif($item['type'] == 4){
            //product
            $suffix     = Mage::getStoreConfig('catalog/seo/product_url_suffix', $storeId);
            $targetPath = "catalog/product/view/id/" . $item['type_related_id'];
            $productId = $item['type_related_id'];
        }else{
            $targetPath = $item['deep_link'];
        }
        $requestPath = $item['deep_link'] . $suffix;
        $rewrite = array();
        $rewrite['store_id'] = $storeId;
        $rewrite['category_id'] = $categoryId;
        $rewrite['product_id'] = $productId;
        $rewrite['id_path'] = $idPath;
        $rewrite['request_path'] = $requestPath;
        $rewrite['target_path'] = $targetPath;
        $rewrite['is_system'] = 0;

        if($this->rewriteExists($rewrite))
        {
            return $this;
        }

        //delete old rewrite(s)
        $this->deleteRewriteByIdPath($idPath,$storeId);

        //don't save if deeplinking is disabled for item
        if (!$item['use_deep_link']) {
            return $this;
        }

        //save rewrite
        try {
            Mage::getModel('core/url_rewrite')
                ->setData($rewrite)
                ->save();
        } catch (Exception $e) {
            //error handling
            $bla = 1;
        };

        return $this;
    }

    public function deleteRewriteByIdPath($idPath, $storeId)
    {
        $collection = Mage::getModel('core/url_rewrite')->getCollection();
        $collection
            ->addFieldToFilter('id_path', $idPath)
            ->addFieldToFilter('store_id', $storeId);

        foreach ($collection as $item) {
            $item->delete();
        }
        return $this;
    }

    public function deleteRewriteByRequestPath($requestPath, $storeId)
    {
        $collection = Mage::getModel('core/url_rewrite')->getCollection();
        $collection
            ->addFieldToFilter('request_path', $requestPath)
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('id_path',array('like'=>'%plugincompanyeasymenu%'))
        ;

        foreach ($collection as $item) {
            $item->delete();
        }
        return $this;
    }

    public function rewriteExists($rewrite)
    {
        $collection = Mage::getModel('core/url_rewrite')->getCollection();
        $collection
            ->addFieldToFilter('store_id',$rewrite['store_id'])
            ->addFieldToFilter('request_path',$rewrite['request_path'])
            ->addFieldToFilter('target_path',$rewrite['target_path']);
        if ($collection->count() > 0) {
            return true;
        }
        return false;
    }
    
    public function getOriginalTitle()
    {
        if(!$this->_originalTitle){
            $this->_initOriginalTitle();
        }
        return $this->_originalTitle;
    }
    
    protected function _initOriginalTitle()
    {
        $this->_originalTitle = $this->_getOriginalTitle();
        return $this;
    }
    
    protected function _getOriginalTitle()
    {
        switch($this->getType()){
            case 1:
                return $this->getRelatedCategoryName();
                break;
            case 2:
                return $this->getRelatedCmsPageTitle();
                break;
            case 4:
                return $this->getRelatedProductName();
                break;
        }
        return $this->getData('title');
    }
    
    public function getRelatedCategoryName()
    {
        return $this->_getRelatedCategoryName();
    }
     
    
    protected function _getRelatedCategoryName()
    {
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            return Mage::getModel('catalog/category')
                ->load($this->getTypeRelatedId())
                ->getName();
        }
        return Mage::getSingleton('catalog/category')
            ->getResource()
            ->getAttributeRawValue($this->getTypeRelatedId(),'name',$this->getStoreId());
    }
    
    public function getRelatedCmsPageTitle()
    {
        return Mage::getSingleton('cms/page')
            ->getResource()
            ->getCmsPageTitleById($this->getTypeRelatedId());
    }
    
    public function getRelatedProductName()
    {
        return $this->_getRelatedProductName();
    }
    
    protected function _getRelatedProductName()
    {
        return Mage::getSingleton('catalog/product')
            ->getResource()
            ->getAttributeRawValue($this->getTypeRelatedId(),'name',$this->getStoreId());
    }
    
    public function getTitle()
    {
        if($this->getUseCustomTitle()){
            return $this->getData('title');
        }
        return $this->getOriginalTitle();
    }

}
