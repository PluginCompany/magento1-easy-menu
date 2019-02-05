<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Easymenu_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('easymenu_select_products_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    public function getStore()
    {
        $storeId = $this->getStoreId();
        return Mage::getModel('core/store')->load($storeId);
    }

    public function getStoreId()
    {
        if ($storeId = Mage::app()->getRequest()->getParam('store')) {
            return $storeId;
        }
        else{
            $stores = Mage::getModel('core/store')->getCollection();
            $stores
                ->addFieldToFilter('website_id', array('neq' => 0));
            $store = $stores->getFirstItem();
            return $store->getId();
        }
    }

    /**
     * Prepare collection to be displayed in the grid
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareCollection()
    {
        $attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection
            ->setStore(Mage::getModel('core/store')->load($this->getStoreId()))
            ->addAttributeToSelect($attributes)
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('url_key')
            ->addAttributeToFilter('visibility',array('neq'=>1))
            ->addStoreFilter()
        ;

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild('add_products_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Add Products'),
                    'onclick'   => 'addProducts()',
                    'class'     => 'add'
                ))
        );
        $this->getChild('reset_filter_button')->setLabel('Reset');
        return $this;
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        $html .= $this->getChildHtml('add_products_button');
        return $html;
    }


    /**
     * Prepare columns
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('sales')->__('ID'),
            'sortable'  => true,
            'width'     => '60',
            'index'     => 'entity_id',
            'column_css_class'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('sales')->__('Product Name'),
            'index'     => 'name',
            'column_css_class'     => 'name'
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('sales')->__('SKU'),
            'width'     => '120',
            'index'     => 'sku',
        ));

        $this->addColumn('url_key', array(
            'header'    => Mage::helper('sales')->__('URL Key'),
            'width'     => '120',
            'index'     => 'url_key',
            'column_css_class'     => 'url_key'
        ));

        $this->addColumn('price', array(
            'header'    => Mage::helper('sales')->__('Price'),
            'column_css_class' => 'price',
            'align'     => 'center',
            'type'      => 'currency',
            'currency_code' => $this->getStore()->getCurrentCurrencyCode(),
            'rate'      => $this->getStore()->getBaseCurrency()->getRate($this->getStore()->getCurrentCurrencyCode()),
            'index'     => 'price',
            'renderer'  => 'adminhtml/sales_order_create_search_grid_renderer_price',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('catalog')->__('Delete'),
            'onclick'  => "console.log('asdasd')",
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/productgrid');
    }

}
