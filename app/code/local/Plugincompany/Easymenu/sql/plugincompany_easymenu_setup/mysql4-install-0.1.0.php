<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Easy Menu, released under the MIT License.
 * See the LICENSE file in the project root.
 */
?>
<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getTable('easymenu/easymenu');
$sql="
create table {$table} (
easymenu_id int not null auto_increment,
store_id int(10),
level int(10),
nav varchar(10),
item_index int(10),
drawnumber int(10),
is_last int(1),
title varchar(255),
link varchar(255),
parent_id int(10),
has_children int(1),
children_ids varchar(255),
url_rewrite_id int(50),
type int(1),
type_related_id int(10),
primary key(easymenu_id)
);
";


$installer->run($sql);

$installer->endSetup();