<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */

$collection = Mage::getModel('easymenu/easymenu')->getCollection();
foreach($collection as $item){
    if($item->getData('title') != $item->getOriginalTitle()){
        $item
            ->setUseCustomTitle(true)
            ->save();
        ;
    }
}
