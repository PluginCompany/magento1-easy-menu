<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Easymenu_Model_Observer
{
    protected function _addNewMenuItemNotification()
    {
        $notification = array();
        $notification['type'] = 'easymenu_newitem';
        $notification['important'] = false;
        $notification['content'] = '<b>There are new menu items avaible</b> go to <a href="%s">Easymenu Management</a> to add the new items.';
        $notification['url'] = 'easymenu/adminhtml_easymenubackend/index';
        Mage::getModel('plugincompany_notifications/notifications')->addSingleNotification($notification);
    }

    public function cmsSaveAfter(Varien_Event_Observer $observer)
    {
        $cms = $observer->getDataObject();
        if ($cms->getCreationTime() == $cms->getUpdateTime()) {
            $this->_addNewMenuItemNotification();
        }
    }

    public function catalogCategorySaveAfter(Varien_Event_Observer $observer)
    {
        $category = $observer->getDataObject();
        if ($category->getUpdatedAt() == $category->getCreatedAt()) {
            $this->_addNewMenuItemNotification();
        }
    }
}