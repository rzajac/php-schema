<?php

use Kicaj\SchemaDump\SchemaDump;
use Kicaj\Tools\Db\DbConnect;
use Kicaj\Tools\Db\DbConnector;

$config = [
        'connection' => DbConnect::getCfg(DbConnector::DB_DRIVER_MYSQL, '127.0.0.1', 'testUser', 'unitTestPass', 'test', '3306'),
        'export_type' => SchemaDump::FORMAT_PHP_ARRAY,
        'drop_before_create' => true,
        'add_if_not_exists' => true,
        'output_file' => 'schema.php',
];

return $config;
