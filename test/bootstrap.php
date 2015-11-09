<?php

define('UNIT_TEST_YOURAPPLICATION_TESTSUITE', 'yes');

// The project root folder
define('PROJECT_PATH', realpath(__DIR__.'/..'));
define('FIXTURE_PATH', PROJECT_PATH.'/test/fixtures');

require_once PROJECT_PATH.'/vendor/autoload.php';
