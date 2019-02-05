<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Notifications_Model_Notifications extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        $this->_init('plugincompany_notifications/notifications');
    }

    public function hasNotifications()
    {
        return $this->getCollection()->count();
    }

    public function addSingleNotification(array $notification)
    {
        $collection = $this->getCollection()->addFieldToFilter('type', $notification['type']);
        foreach ($collection as $item) {
            $item->delete();
        }
        $this->setData($notification)->save();
        return $this;

    }

}