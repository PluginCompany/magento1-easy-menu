<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
class Plugincompany_Easymenu_Block_Adminhtml_Categories_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories{

    public function __construct()
    {
        $this->setTemplate('easymenu/categories.phtml');
    }
}
