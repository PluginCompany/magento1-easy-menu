<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
class Plugincompany_Notifications_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function goUrlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $notification = Mage::getModel('plugincompany_notifications/notifications')->load($id);
        $url = $notification->getUrl();
        $notification->delete();
        $this->_redirect($url);
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