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

namespace Kicaj\Schema\Database\MySQL;

use Kicaj\DbKit\DbConnector;
use Kicaj\Schema\Itf\DatabaseItf;
use Kicaj\Schema\SchemaException;
use mysqli;

/**
 * MySQL database driver.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class MySQL implements DbConnector, DatabaseItf
{
    /** MySQL integer types */
    const TYPE_TINYINT = 'tinyint';
    const TYPE_SMALLINT = 'smallint';
    const TYPE_MEDIUMINT = 'mediumint';
    const TYPE_INT = 'int';
    const TYPE_BIGINT = 'bigint';
    const TYPE_DECIMAL = 'decimal';

    /** MySQL binary types */
    const TYPE_BIT = 'bit';
    const TYPE_BINARY = 'binary';
    const TYPE_VARBINARY = 'varbinary';

    /** MySQL string types */
    const TYPE_CHAR = 'char';
    const TYPE_VARCHAR = 'varchar';
    const TYPE_TEXT = 'text';
    const TYPE_TINYTEXT = 'tinytext';
    const TYPE_LONGTEXT = 'longtext';
    const TYPE_MEDIUMTEXT = 'mediumtext';

    /** MySQL blob types */
    const TYPE_BLOB = 'blob';
    const TYPE_LONGBLOB = 'longblob';
    const TYPE_MEDIUMBLOB = 'mediumblob';
    const TYPE_TINYBLOB = 'tinyblob';

    /** MySQL blob types */
    const TYPE_FLOAT = 'float';
    const TYPE_DOUBLE = 'double';

    /** MySQL array types */
    const TYPE_ENUM = 'enum';
    const TYPE_SET = 'set';

    /** MySQL date and time types */
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATE = 'date';
    const TYPE_TIME = 'time';
    const TYPE_YEAR = 'year';

    /**
     * The database name.
     *
     * @var string
     */
    protected $dbName;

    /**
     * The database driver.
     *
     * @var mysqli
     */
    protected $mysqli;

    /**
     * Database configuration.
     *
     * @var array
     */
    protected $dbConfig;

    public function dbSetup(array $dbConfig)
    {
        $this->dbConfig = $dbConfig;

        return $this;
    }

    public function dbConnect()
    {
        $this->dbName = $this->dbConfig[DbConnector::DB_CFG_DATABASE];

        mysqli_report(MYSQLI_REPORT_STRICT);

        // Connect to database
        try {
            $this->mysqli = new mysqli(
                $this->dbConfig[DbConnector::DB_CFG_HOST],
                $this->dbConfig[DbConnector::DB_CFG_USERNAME],
                $this->dbConfig[DbConnector::DB_CFG_PASSWORD],
                $this->dbName,
                $this->dbConfig[DbConnector::DB_CFG_PORT]);
        } catch (\Exception $e) {
            throw SchemaException::makeFromException($e);
        }

        return $this;
    }

    public function dbClose()
    {
        if ($this->mysqli) {
            $this->mysqli->close();
            $this->mysqli = null;
        }
    }

    /**
     * Return table and view names form the database.
     *
     * @throws SchemaException
     *
     * @return array
     */
    protected function getTableAndViewNames()
    {
        $resp = $this->runQuery(sprintf('SHOW FULL TABLES FROM `%s`', $this->dbName));

        $tableAndViewNames = [];
        while ($row = $resp->fetch_assoc()) {
            $tableAndViewNames[] = array_change_key_case($row);
        }

        return $tableAndViewNames;
    }

    public function dbGetTableNames()
    {
        $tableAndViewNames = $this->getTableAndViewNames();

        $tableNames = [];
        foreach ($tableAndViewNames as $table) {
            if ($table['table_type'] == 'BASE TABLE') {
                $tableNames[] = $table['tables_in_' . mb_strtolower($this->dbName)];
            }
        }

        return $tableNames;
    }

    public function dbGetViewNames()
    {
        $tableAndViewNames = $this->getTableAndViewNames();

        $viewNames = [];
        foreach ($tableAndViewNames as $view) {
            if ($view['table_type'] == 'VIEW') {
                $viewNames[] = $view['tables_in_' . mb_strtolower($this->dbName)];
            }
        }

        return $viewNames;
    }

    public function dbGetTableDefinition($tableName)
    {
        $result = $this->runQuery(sprintf('SHOW CREATE TABLE `%s`', $tableName));
        $createStatement = $this->getRowsArray($result);
        $createStatement = array_pop($createStatement);


        // We can get either table or view
        if (array_key_exists('Create Table', $createStatement)) {
            $key = 'Create Table';
        } elseif (array_key_exists('Create View', $createStatement)) {
            $key = 'Create View';
        } else {
            throw new SchemaException('Was not able to figure out create statement for: '. $tableName);
        }

        return new Table($createStatement[$key], $this);
    }

    /**
     * Run SQL query.
     *
     * @param string $sql The SQL query.
     *
     * @throws SchemaException
     *
     * @return bool|\mysqli_result
     */
    public function runQuery($sql)
    {
        $result = $this->mysqli->query($sql);
        if ($result === false) {
            throw new SchemaException($this->mysqli->error);
        }

        return $result;
    }

    /**
     * Get all rows from the DB result.
     *
     * @param \mysqli_result|bool $result The return of query method.
     *
     * @return array The array of SQL rows
     */
    public function getRowsArray($result)
    {
        $rows = [];

        if (!$result) {
            return $rows;
        }

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $result->free();

        return $rows;
    }
}
