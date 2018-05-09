<?php declare(strict_types=1);

use Kicaj\Schema\Schema;
use Kicaj\Schema\Database\DbConnect;
use Kicaj\Schema\Database\DbConnector;

$config = [
    Schema::CONFIG_KEY_CONNECTION => DbConnect::getCfg(DbConnector::DB_DRIVER_MYSQL, '127.0.0.1', 'testUser', 'unitTestPass', 'test', 3306),
    Schema::CONFIG_KEY_EXPORT_FORMAT => Schema::FORMAT_PHP_ARRAY,
    Schema::CONFIG_KEY_AINE => true,
    Schema::CONFIG_KEY_OUTPUT_FILE => 'schema.php',
];

return $config;
