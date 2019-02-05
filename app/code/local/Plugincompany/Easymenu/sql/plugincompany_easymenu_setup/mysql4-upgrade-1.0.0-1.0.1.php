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
alter table {$table}
ADD COLUMN use_deep_link int
;
";

$installer->run($sql);

$installer->endSetup();