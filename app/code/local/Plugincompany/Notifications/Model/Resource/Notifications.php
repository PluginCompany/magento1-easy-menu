<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Notifications_Model_Resource_Notifications extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('plugincompany_notifications/notifications', 'notifications_id');
    }

}