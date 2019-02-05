<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Easymenu_Block_Adminhtml_Cms_Page_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('easymenu_cms_page_grid');
        $this->setDefaultSort('identifier');
        $this->setDefaultDir('ASC');
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

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cms/page')->getCollection();
        /* @var $collection Mage_Cms_Model_Mysql4_Page_Collection */
        $collection->addStoreFilter($this->getStore());
        $collection->load();
        $ids = $collection->getAllIds();


        //need to exectute query again
        //because of bug in countSelectSql in CMS page resource model
        $collection = Mage::getModel('cms/page')
            ->getCollection()
            ->addFieldToFilter('page_id',array('in'=>$ids))
        ;

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header'    => Mage::helper('cms')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
            'column_css_class'     => 'cat_title'
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('cms')->__('URL Key'),
            'align'     => 'left',
            'index'     => 'identifier',
            'column_css_class'     => 'cat_url'
        ));


        $this->addColumn('is_active', array(
            'header'    => Mage::helper('cms')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('cms/page')->getAvailableStatuses()
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild('add_cms_pages_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Add Pages'),
                    'onclick'   => 'addCmsPages()',
                    'class'     => 'add'
                ))
        );
        $this->getChild('reset_filter_button')->setLabel('Reset');
        return $this;
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        $html .= $this->getChildHtml('add_cms_pages_button');
        return $html;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('page_id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('catalog')->__('Delete'),
            'onclick'  => "console.log('asdasd')",
            'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        return $this;
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/cmspagegrid',array('_current'=>true));
    }
}
