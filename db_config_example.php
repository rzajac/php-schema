<?php

$config = array
(
	'connection'    => array
	(
		'user'     => 'root',
		'pass'     => '',
		'host'     => '127.0.0.1',
		'port'     => '3306',
		'database' => 'test'
	),
	'export_type' => SchemaExp::FORMAT_PHP_ARRAY_STR,
	'drop_before_create' => TRUE,
	'add_if_not_exists' => TRUE,
	'output_file' => './fixtures/schema/schema.php'
);

return $config;
