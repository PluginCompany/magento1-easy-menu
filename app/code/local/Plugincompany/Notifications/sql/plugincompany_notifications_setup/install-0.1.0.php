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

$table = $installer->getTable('plugincompany_notifications/notifications');

$sql="
drop table if exists {$table};
create table {$table}(
notifications_id int not null auto_increment,
type varchar(255),
important int(1),
content text,
url varchar(255),
primary key(notifications_id)
);
";

$installer->run($sql);

$installer->endSetup();