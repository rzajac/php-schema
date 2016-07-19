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
namespace Kicaj\Schema\Database;

use Kicaj\Schema\Database\Driver\MySQL;
use Kicaj\Schema\Schema;
use Kicaj\Schema\SchemaException;
use Kicaj\Schema\SchemaGetter;
use Kicaj\Tools\Db\DatabaseException;
use Kicaj\Tools\Db\DbConnect;
use Kicaj\Tools\Db\DbConnector;

/**
 * Helper class for getting database driver.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
final class SchemaFactory
{
    /**
     * Instances.
     *
     * @var SchemaGetter[]
     */
    private static $instances = [];

    /**
     * Database factory.
     *
     * It returns the same instance for the same config.
     *
     * @see \Kicaj\Tools\Itf\DbConnect
     *
     * @param array $dbConfig The database configuration
     * @param bool  $connect  Set to true to also connect to the database
     *
     * @throws SchemaException
     * @throws DatabaseException
     *
     * @return SchemaGetter
     */
    public static function factory(array $dbConfig, $connect = true)
    {
        $key = md5(json_encode($dbConfig));

        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }

        switch (DbConnect::getDriver($dbConfig[Schema::CONFIG_KEY_CONNECTION])) {
            case DbConnector::DB_DRIVER_MYSQL:
                self::$instances[$key] = new MySQL();
                break;

            default:
                throw new SchemaException('unknown database driver name: ' . $dbConfig[Schema::CONFIG_KEY_CONNECTION][DbConnector::DB_CFG_DRIVER]);
        }

        self::$instances[$key]->dbSetup($dbConfig[Schema::CONFIG_KEY_CONNECTION]);

        if ($connect) {
            self::$instances[$key]->dbConnect();
        }

        return self::$instances[$key];
    }

    // @codeCoverageIgnoreStart
    /**
     * Reset instances cache.
     *
     * This is used only during unit tests.
     */
    public static function _resetInstances()
    {
        self::$instances = [];
    }
    // @codeCoverageIgnoreEnd
}
