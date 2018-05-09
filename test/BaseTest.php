<?php declare(strict_types=1);

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
namespace Kicaj\Test\Schema;

use Kicaj\Schema\Schema;
use Kicaj\Test\Helper\TestCase\DbTestCase;

/**
 * Base test case.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
abstract class BaseTest extends DbTestCase
{
    /**
     * Get schema export config.
     *
     * @param string $testDbName   The name of database connection details form phpunit.xml.
     * @param string $exportFormat The schema export format.
     *
     * @return array
     */
    public static function getSchemaConfig($testDbName, $exportFormat = Schema::FORMAT_PHP_ARRAY)
    {
        return [
            Schema::CONFIG_KEY_CONNECTION => self::dbGetConfig($testDbName),
            Schema::CONFIG_KEY_EXPORT_FORMAT => $exportFormat,
            Schema::CONFIG_KEY_AINE => false,
            Schema::CONFIG_KEY_OUTPUT_FILE => 'tmp/schema.php',
        ];
    }
}
