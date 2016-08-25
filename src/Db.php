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

namespace Kicaj\Schema;


use Kicaj\DbKit\DbConnect;
use Kicaj\DbKit\DbConnector;
use Kicaj\Schema\Database\MySQL\MySQL;
use Kicaj\Schema\Itf\DatabaseItf;
use Kicaj\Schema\Itf\TableItf;

/**
 * Class representing database.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
final class Db
{
    /**
     * Instances.
     *
     * @var DbConnector[]
     */
    private static $instances = [];

    /**
     * Database driver.
     *
     * @var DatabaseItf
     */
    protected $dbDriver;

    /**
     * Database factory.
     *
     * It returns the same instance for the same config.
     *
     * @see \Kicaj\Tools\Itf\DbConnect
     *
     * @param array $dbConfig The database configuration.
     *
     * @throws SchemaException
     *
     * @return Db
     */
    public static function factory(array $dbConfig)
    {
        $key = md5(json_encode($dbConfig));

        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }

        switch (DbConnect::getDriver($dbConfig[Schema::CONFIG_KEY_CONNECTION])) {
            case DbConnector::DB_DRIVER_MYSQL:
                $driver = new MySQL();
                break;

            default:
                throw new SchemaException('Unknown database driver name: ' . DbConnect::getDriver($dbConfig[Schema::CONFIG_KEY_CONNECTION]));
        }

        $driver->dbSetup($dbConfig[Schema::CONFIG_KEY_CONNECTION])->dbConnect();
        self::$instances[$key] = new self($driver);

        return self::$instances[$key];
    }

    /**
     * Constructor.
     *
     * Use factory to create the object.
     *
     * @param DatabaseItf $driver The database driver.
     */
    private function __construct(DatabaseItf $driver)
    {
        $this->dbDriver = $driver;
    }

    /**
     * Get database table names.
     *
     * @throws SchemaException
     *
     * @return string[] The table names.
     */
    public function dbGetTableNames()
    {
        return $this->dbDriver->dbGetTableNames();
    }

    /**
     * Get database view names.
     *
     * @throws SchemaException
     *
     * @return string[] The view names.
     */
    public function dbGetViewNames()
    {
        return $this->dbDriver->dbGetViewNames();
    }

    /**
     * Return table definition for given database table.
     *
     * @param string $tableName The database table name.
     *
     * @throws SchemaException
     *
     * @return TableItf
     */
    public function dbGetTableDefinition($tableName)
    {
        return $this->dbDriver->dbGetTableDefinition($tableName);
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
