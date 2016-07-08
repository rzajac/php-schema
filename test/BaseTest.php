<?php

/**
 * Copyright 2015 Rafal Zajac <rzajac@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
namespace Kicaj\Test\SchemaDump;

use Kicaj\SchemaDump\SchemaDump;
use Kicaj\Test\Helper\TestCase\DbTestCase;

/**
 * Base test case.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
abstract class BaseTest extends DbTestCase
{
    /**
     * Get schema dump config.
     *
     * @param string $testDbName The name of database connection details form phpunit.xml.
     * @param string $exportType The schema export type.
     *
     * @return array
     */
    public static function getDbConfig($testDbName, $exportType = SchemaDump::FORMAT_PHP_ARRAY)
    {
        return [
            'connection' => self::dbGetConfig($testDbName),
            'export_type' => $exportType,
            'add_if_not_exists' => false,
            'output_file' => 'tmp/schema.php',
        ];
    }
}
