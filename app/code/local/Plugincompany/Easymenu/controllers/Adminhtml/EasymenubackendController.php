<?php
class Plugincompany_Easymenu_Adminhtml_EasymenubackendController extends Mage_Adminhtml_Controller_Action
{

    protected function _initProduct()
    {
        $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId());
        Mage::register('product', $product);
        Mage::register('current_product', $product);
        return $product;
    }
    protected function getStoreId()
    {
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
	public function indexAction()
    {
       $this->_initProduct();
       $this->loadLayout();
	   $this->_title($this->__("Manage menu"));
       $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true);
	   $this->renderLayout();
    }

    public function saveAction()
    {
        try {
            $post = Mage::app()->getRequest()->getPost();

            $data = json_decode($post['menu']);

            $storeId = $this->getStoreId();
            $menu = array();
            foreach ($data as $item) {

                $link = $item->link;
                if ($item->type == "1") {
                    $link = Mage::getModel('catalog/category')->setStoreId($storeId)->load($item->id)->getUrlPath();
                    $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix', $storeId);
                    $link = substr($link, 0, strlen($link) - strlen($suffix));
                }
                $menu[] = array
                (
                    'store_id' => $storeId,
                    'level' => $item->depth - 2,
                    'nav' => $item->nav,
                    'drawnumber' => $item->drawNumber,
                    'title' => $item->title,
                    'parent_id' => NULL,
                    'has_children' => NULL,
                    'children_ids' => NULL,
                    'url_rewrite_id' => NULL,
                    'type' => $item->type,
                    'type_related_id' => $item->id,
                    'has_children' => $item->hasChildren,
                    'item_index' => $item->index + 1,
                    'is_last' => $item->isLast,
                    'link' => $link,
                    'deep_link' => $item->deepLink,
                    'use_deep_link' => $item->useDeepLink,
                    'use_custom_title' => $item->useCustomTitle
                );


            }

            //delete old menu
            Mage::getModel('easymenu/easymenu')->deleteByStoreId($storeId);

            //add new menu
            foreach ($menu as $menuItem) {
                $mItem = Mage::getModel('easymenu/easymenu');
                $mItem->setData($menuItem);
                $mItem->createUrlRewrite($menuItem);
                $mItem->save();
            }
        } catch (Exception $e){
            $this->getResponse()->setBody(json_encode(array('Something went wrong','error')));
        };


        //clean block html cache after save
        Mage::app()->cleanCache(array('block_html'));

        $this->getResponse()->setBody(json_encode(array('Menu succesfully saved','success'))) ;
    }

    public function deleteAction()
    {
        $storeId = $this->getStoreId();
        Mage::getModel('easymenu/easymenu')->deleteByStoreId($storeId);
        Mage::getSingleton('core/session')->addSuccess('Menu succesfully deleted');
    }

    public function productgridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('easymenu/adminhtml_products_grid')->toHtml()
        );
    }

    public function cmspagegridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('easymenu/adminhtml_cms_page_grid')->toHtml()
        );
    }

    public function categoriesJsonAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('easymenu/adminhtml_categories_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    public function copystoremenuAction()
    {
        $post = $this->getRequest()->getPost();

        $fromId = $post['fromStore'];
        $toId = $post['toStore'];

        $fStore = Mage::getModel('core/store')->load($fromId);
        $fRoot = $fStore->getGroup()->getRootCategoryId();

        $tStore = Mage::getModel('core/store')->load($toId);
        $tRoot = $tStore->getGroup()->getRootCategoryId();

        if ($fRoot != $tRoot) {
            $this->getResponse()->setBody(
                json_encode(array('status' => 'error', 'message' => "Can't copy menu because store view root categories don't match"))
            );
        }else{
            $html = $this
                ->getLayout()
                ->createBlock('easymenu/adminhtml_easymenubackend')
                ->setStoreId($toId)
                ->setItemTitleStoreId($fromId)
                ->getCurrentMenu();
            if($html){
                $this->getResponse()->setBody(
                    json_encode(array('status' => 'success', 'message' => "Menu successfully copied",'html'=>$html))
                );
            }else{
                $this->getResponse()->setBody(
                    json_encode(array('status' => 'notice', 'message' => "The menu of the selected store is empty"))
                );
            }
        }
    }

    /**
     * Check if admin has permissions to visit related pages
     * @access protected
     * @return boolean
     * @author Milan Simek
     */
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')
            ->isAllowed('system/easymenubackend');
    }
}