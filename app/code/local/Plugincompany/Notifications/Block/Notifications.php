<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Notifications_Block_Notifications extends Mage_Adminhtml_Block_Template
{

    public function hasNotifications()
    {
        $notificationCount = Mage::getModel('plugincompany_notifications/notifications')->hasNotifications();
        return $notificationCount;
    }

    public function getNotifications()
    {
        return Mage::getModel('plugincompany_notifications/notifications')->getCollection();
    }
}
