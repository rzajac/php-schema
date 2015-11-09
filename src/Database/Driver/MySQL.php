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
namespace Kicaj\SchemaDump\Database\Driver;

use Exception;
use Kicaj\SchemaDump\ColumnDefinition;
use Kicaj\SchemaDump\SchemaDump;
use Kicaj\SchemaDump\SchemaException;
use Kicaj\SchemaDump\SchemaGetter;
use Kicaj\SchemaDump\TableDefinition;
use Kicaj\Tools\Traits\Error;
use mysqli;

/**
 * MySQL database schema driver.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class MySQL implements SchemaGetter
{
    use Error;

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

    /** MySQL date and time types */
    const TYPE_TIMESTAMP = 'timestamp';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATE = 'date';
    const TYPE_TIME = 'time';
    const TYPE_YEAR = 'year';

    /** MySQL index types */
    const INDEX_PRIMARY = 'PRIMARY';
    const INDEX_UNIQUE = 'UNIQUE';
    const INDEX_KEY = 'KEY';

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

    /**
     * Configure database.
     *
     * The database connection info array must have keys:
     *
     * - host: string
     * - username: string
     * - password: string
     * - database: string
     * - port: int
     * - debug: bool
     *
     * @param array $dbConfig The database configuration
     *
     * @return MySQL
     */
    public function dbSetup(array $dbConfig)
    {
        $this->dbConfig = $dbConfig;

        return $this;
    }

    /**
     * Connect to database.
     *
     * @return bool Returns true on success.
     */
    public function dbConnect()
    {
        // Database connection config
        $host = $this->dbConfig['host'];
        $port = $this->dbConfig['port'];
        $user = $this->dbConfig['username'];
        $pass = $this->dbConfig['password'];

        $this->dbName = $this->dbConfig['database'];

        mysqli_report(MYSQLI_REPORT_STRICT);

        // Connect to database
        try {
            $this->mysqli = new mysqli($host, $user, $pass, $this->dbName, $port);
        } catch (\Exception $e) {
            return $this->addError($e);
        }

        return true;
    }

    /**
     * Close database connection.
     *
     * @return bool
     */
    public function dbClose()
    {
        if ($this->mysqli) {
            $ret = $this->mysqli->close();
            $this->mysqli = null;

            return $ret;
        }

        return true;
    }

    /**
     * Get database table names.
     *
     * @return string[] The table names
     */
    public function dbGetTableNames()
    {
        $resp = $this->mysqli->query('SHOW TABLES');
        $tableNames = $this->getRowsArray($resp);

        $ret = [];
        foreach ($tableNames as $tableName) {
            $ret[] = $tableName['Tables_in_'.$this->dbName];
        }

        return $ret;
    }

    /**
     * Get create statement for the given table name.
     *
     * Method returns associative array where keys are table names and values are arrays with keys:
     *
     * - create - CREATE TABLE or VIEW statement
     * - type   - table, view ( one of the self::CREATE_TYPE_* )
     * - name   - table name
     *
     * @param string $tableName      The table name to get CREATE statement for
     * @param bool   $addIfNotExists Set to true to add IF NOT EXIST to CREATE TABLE
     *
     * @return array
     */
    public function dbGetCreateStatement($tableName, $addIfNotExists = false)
    {
        $resp = $this->mysqli->query('SHOW CREATE TABLE '.$tableName);
        $createStatementResp = $this->getRowsArray($resp);

        $createStatement = array_pop($createStatementResp);

        if (!is_array($createStatement)) {
            return [];
        }

        // We can get either table or view
        if (array_key_exists('Create Table', $createStatement)) {
            $type = self::CREATE_TYPE_TABLE;
            $key = 'Create Table';
        } elseif (array_key_exists('Create View', $createStatement)) {
            $type = self::CREATE_TYPE_VIEW;
            $key = 'Create View';
        } else {
            // Error
            return [];
        }

        $ret = [
            'create' => $createStatement[$key],
            'drop' => $this->dbGetTableDropCommand($tableName),
            'type' => $type,
            'name' => $tableName,
        ];

        return $this->fixCreateStatement($ret, $addIfNotExists);
    }

    /**
     * Get create statements for all database tables.
     *
     * @param bool $addIfNotExists Set to true to add IF NOT EXIST to CREATE TABLE
     *
     * @return array See SchemaGetter::dbGetCreateStatement
     */
    public function dbGetCreateStatements($addIfNotExists = false)
    {
        $tableNames = $this->dbGetTableNames();

        $createStatements = [];
        foreach ($tableNames as $tableName) {
            $createStatement = $this->dbGetCreateStatement($tableName, $addIfNotExists);

            if ($createStatement) {
                $createStatements[$tableName] = $createStatement['create'];
            }
        }

        return $createStatements;
    }

    /**
     * Get database table drop command.
     *
     * @param string $tableName The table name
     *
     * @return string
     */
    public function dbGetTableDropCommand($tableName)
    {
        return 'DROP TABLE IF EXISTS `'.$tableName.'`;';
    }

    /**
     * Return table definitions for given database table.
     *
     * @param string $tableName The database table name
     *
     * @return TableDefinition
     */
    public function dbGetTableDefinition($tableName)
    {
        $lines = explode("\n", $this->dbGetCreateStatement($tableName)['create']);

        // Remove CREATE TABLE line
        array_shift($lines);

        $tableDef = new TableDefinition($tableName);

        foreach ($lines as $line) {
            $line = trim($line);

            // Column definitions start with `
            if (strpos($line, '`') === 0) {
                $colDef = $this->parseColumn($line, $tableName);
                $tableDef->addColumn($colDef);
            } else if (strpos($line, 'PRIMARY KEY') === 0) {
                $index = self::parseIndex($line);
                $index[1] = self::INDEX_PRIMARY;
                $tableDef->addIndex($index);
            } else if (strpos($line, 'UNIQUE KEY') === 0) {
                $index = self::parseIndex($line);
                $index[1] = self::INDEX_UNIQUE;
                $tableDef->addIndex($index);
            } else if (strpos($line, 'KEY') === 0) {
                $index = self::parseIndex($line);
                $index[1] = self::INDEX_KEY;
                $tableDef->addIndex($index);
            }
        }

        return $tableDef;
    }

    /**
     * Parse index definition.
     *
     * @param string $keyDef
     *
     * @return array
     * @throws SchemaException
     */
    public static function parseIndex($keyDef)
    {
        preg_match('/(.*)?KEY (?:`(.*?)` )?\((.*)\)/', $keyDef, $matches);

        if (count($matches) != 4) {
            throw new SchemaException('cannot parse table index: '.$keyDef);
        }

        $type = trim($matches[1]);
        $name = trim($matches[2]);
        $colNames = explode(',', str_replace('`', '', $matches[3]));

        return [$name, $type, $colNames];
    }

    /**
     * Parse database column definition.
     *
     * @param string $columnDef The column definition as returned by SHOW CREATE TABLE query
     * @param string $tableName The database table name
     *
     * @throws SchemaException
     *
     * @return ColumnDefinition
     */
    public static function parseColumn($columnDef, $tableName)
    {
        // Remove unnecessary whitespace
        $columnDef = trim($columnDef);

        // Explode. First two indexes are always column name and its type
        $definition = explode(' ', $columnDef);
        $colName = trim($definition[0], '`');
        $colType = $definition[1];
        $colExtra = '';

        // If more then 2 we have extra definitions for the column
        if (count($definition) > 2) {
            // We need it as a string
            $colExtra = implode(' ', array_slice($definition, 2));
        }

        $colDef = ColumnDefinition::make($colName, $tableName);

        // Figure out the extra parameters first so we can access to undefined not null and so on
        static::setColDefExtra($colDef, $colExtra);
        // Set MySQL and PHP types for the column
        static::mySQLToPhpType($colDef, $colType);

        if ($colDef->getDbType() === self::TYPE_ENUM) {
            $colType = $colType.' '.$colExtra;
        }

        // Set column lengths
        static::mySQLTypeLengths($colDef, $colType);
        // Set bounds for type
        static::setTypeBounds($colDef);

        return $colDef;
    }

    /**
     * Parse additional column definition.
     *
     * @param ColumnDefinition $colDef      The column definition
     * @param string           $colDefExtra The extra column definition
     *
     * @throws SchemaException
     */
    public static function setColDefExtra(ColumnDefinition $colDef, $colDefExtra)
    {
        if (strpos($colDefExtra, 'unsigned') !== false) {
            $colDef->setIsUnsigned();
        }

        if (strpos($colDefExtra, 'NOT NULL') !== false) {
            $colDef->setNotNull();
        }

        if (strpos($colDefExtra, 'AUTO_INCREMENT') !== false) {
            $colDef->setIsAutoincrement();
        }

        if (strpos($colDefExtra, 'DEFAULT ') !== false) {
            preg_match('/DEFAULT (.*)[ ,]/', $colDefExtra, $matches);
            if (count($matches) != 2) {
                throw new SchemaException('could not decipher DEFAULT: ' . $colDefExtra);
            }
            $defaultValue = trim($matches[1]);
            $defaultValue = trim($defaultValue, '\'');
            if ($defaultValue == 'NULL') {
                $defaultValue = null;
            }
            $colDef->setDefaultValue($defaultValue);
        }
    }

    /**
     * Parse type length values.
     *
     * @param ColumnDefinition $colDef  The column definition
     * @param string           $typeDef The type definition from database
     *
     * @throws SchemaException
     */
    public static function mySQLTypeLengths(ColumnDefinition $colDef, $typeDef)
    {
        // We return if no lengths
        if (strpos($typeDef, '(') === false) {
            return;
        }

        // Types that we ignore
        switch ($colDef->getDbType()) {
            case self::TYPE_TINYINT:
            case self::TYPE_SMALLINT:
            case self::TYPE_MEDIUMINT:
            case self::TYPE_INT:
            case self::TYPE_BIGINT:
            case self::TYPE_FLOAT:
            case self::TYPE_DOUBLE:
            case self::TYPE_DECIMAL:
            case self::TYPE_YEAR:
                return;
            break;
        }

        preg_match('/.*\((.*)\).*/', $typeDef, $matches);

        if ($colDef->getDbType() === self::TYPE_ENUM) {
            $matches = explode(',', $matches[1]);
            foreach ($matches as &$value) {
                $value = trim($value, '\'');
            }
            $colDef->setValidValues($matches);
            return;
        }

        if (count($matches) === 2) {

            // NOTE: we ignore some types like decimal(5,2) so we
            // don't check for this kind of strings.

            $lengthDef = $matches[1];
            $left = $lengthDef;
            $colDef->setMinLength(0)->setMaxLength((int)$left);
        }
    }

    /**
     * Maps MySQL types to PHP types.
     *
     * @param ColumnDefinition $colDef   The column definition
     * @param string           $mysqlDef The type definition from database
     *
     * @throws SchemaException
     */
    public static function mySQLToPhpType(ColumnDefinition $colDef, $mysqlDef)
    {
        // Integer types

        if (strpos($mysqlDef, self::TYPE_TINYINT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_INT)
                ->setDbType(self::TYPE_TINYINT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_MEDIUMINT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_INT)
                ->setDbType(self::TYPE_MEDIUMINT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_SMALLINT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_INT)
                ->setDbType(self::TYPE_SMALLINT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_BIGINT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_INT)
                ->setDbType(self::TYPE_BIGINT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_INT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_INT)
                ->setDbType(self::TYPE_INT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_DECIMAL) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_INT)
                ->setDbType(self::TYPE_DECIMAL);
            return;
        }

        // Bit types

        if (strpos($mysqlDef, self::TYPE_BIT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_BINARY)
                ->setDbType(self::TYPE_BIT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_BINARY) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_BINARY)
                ->setDbType(self::TYPE_BINARY);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_VARBINARY) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_BINARY)
                ->setDbType(self::TYPE_VARBINARY);
            return;
        }

        // String types

        if (strpos($mysqlDef, self::TYPE_VARCHAR) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_VARCHAR);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_CHAR) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_CHAR);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_TEXT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_TEXT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_TINYTEXT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_TINYTEXT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_LONGTEXT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_LONGTEXT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_MEDIUMTEXT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_MEDIUMTEXT);
            return;
        }

        // Date time types

        if (strpos($mysqlDef, self::TYPE_TIMESTAMP) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_TIMESTAMP)
                ->setDbType(self::TYPE_TIMESTAMP);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_TIME) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_TIME)
                ->setDbType(self::TYPE_TIME);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_DATETIME) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_DATETIME)
                ->setDbType(self::TYPE_DATETIME);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_DATE) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_DATE)
                ->setDbType(self::TYPE_DATE);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_YEAR) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_YEAR)
                ->setDbType(self::TYPE_YEAR);
            return;
        }

        // Blob types

        if (strpos($mysqlDef, self::TYPE_MEDIUMBLOB) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_MEDIUMBLOB);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_TINYBLOB) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_TINYBLOB);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_LONGBLOB) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_LONGBLOB);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_BLOB) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_STRING)
                ->setDbType(self::TYPE_BLOB);
            return;
        }

        // Float types

        if (strpos($mysqlDef, self::TYPE_FLOAT) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_FLOAT)
                ->setDbType(self::TYPE_FLOAT);
            return;
        }

        if (strpos($mysqlDef, self::TYPE_DOUBLE) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_FLOAT)
                ->setDbType(self::TYPE_DOUBLE);
            return;
        }

        // Array types

        if (strpos($mysqlDef, self::TYPE_ENUM) === 0) {
            $colDef
                ->setPhpType(SchemaDump::PHP_TYPE_ARRAY)
                ->setDbType(self::TYPE_ENUM);
            return;
        }

        throw new SchemaException('unknown type: '.$mysqlDef);
    }

    /**
     * Set type bounds.
     *
     * ColumnDefinition instance must have MYSQL type and undefined set
     * before calling this method.
     *
     * @param ColumnDefinition $colDef The column definition
     *
     * @throws SchemaException
     */
    public static function setTypeBounds(ColumnDefinition $colDef)
    {
        switch ($colDef->getDbType()) {
            case self::TYPE_TINYINT:
                if ($colDef->isUnsigned()) {
                    $colDef->setMinValue(0)->setMaxValue(255);
                } else {
                    $colDef->setMinValue(-128)->setMaxValue(127);
                }
                break;

            case self::TYPE_SMALLINT:
                if ($colDef->isUnsigned()) {
                    $colDef->setMinValue(0)->setMaxValue(65535);
                } else {
                    $colDef->setMinValue(-32768)->setMaxValue(32767);
                }
                break;

            case self::TYPE_MEDIUMINT:
                if ($colDef->isUnsigned()) {
                    $colDef->setMinValue(0)->setMaxValue(16777215);
                } else {
                    $colDef->setMinValue(-8388608)->setMaxValue(8388607);
                }
                break;

            case self::TYPE_INT:
                if ($colDef->isUnsigned()) {
                    $colDef->setMinValue(0)->setMaxValue(4294967295);
                } else {
                    $colDef->setMinValue(-2147483648)->setMaxValue(2147483647);
                }
                break;

            case self::TYPE_BIGINT:
                if ($colDef->isUnsigned()) {
                    $colDef->setMinValue(0)->setMaxValue(18446744073709551615);
                } else {
                    $colDef->setMinValue(-9223372036854775808)->setMaxValue(9223372036854775807);
                }
                break;

            case self::TYPE_TINYTEXT:
                $colDef->setMinLength(0)->setMaxLength(255);
                break;

            case self::TYPE_TEXT:
                $colDef->setMinLength(0)->setMaxLength(65535);
                break;

            case self::TYPE_MEDIUMTEXT:
                $colDef->setMinLength(0)->setMaxLength(16777215);
                break;

            case self::TYPE_LONGTEXT:
                $colDef->setMinLength(0)->setMaxLength(4294967295);
                break;

            case self::TYPE_TINYBLOB:
                $colDef->setMinLength(0)->setMaxLength(255);
                break;

            case self::TYPE_MEDIUMBLOB:
                $colDef->setMinLength(0)->setMaxLength(16777215);
                break;

            case self::TYPE_BLOB:
                $colDef->setMinLength(0)->setMaxLength(65535);
                break;

            case self::TYPE_LONGBLOB:
                $colDef->setMinLength(0)->setMaxLength(4294967295);
                break;

            case self::TYPE_TIMESTAMP:
                $colDef->setMinValue(0)->setMaxValue(2147483647);
                break;

            case self::TYPE_DATE:
                $colDef->setMinValue('1000-01-01')
                    ->setMaxValue('9999-12-31');
                break;

            case self::TYPE_DATETIME:
                $colDef->setMinValue('1000-01-01 00:00:00')
                    ->setMaxValue('9999-12-31 23:59:59');
                break;

            case self::TYPE_YEAR:
                $colDef->setMinValue(1901)
                    ->setMaxValue(2155);
                break;

            case self::TYPE_CHAR:
            case self::TYPE_VARCHAR:
            case self::TYPE_FLOAT:
            case self::TYPE_DOUBLE:
            case self::TYPE_DECIMAL:
            case self::TYPE_BIT:
            case self::TYPE_BINARY:
            case self::TYPE_VARBINARY:
            case self::TYPE_TIME:
            case self::TYPE_ENUM:
                // No bounds
                break;

            default:
                throw new SchemaException('unknown database type: '.$colDef->getDbType());
        }
    }

    /**
     * Fix and rewrite CREATE statements if needed.
     *
     * @param array $createStatement The CREATE statement description array
     * @param bool  $addIfNotExists
     *
     * @return array The changed CREATE statement description array
     */
    private function fixCreateStatement(array $createStatement, $addIfNotExists = false)
    {
        $createStatement['create'] = preg_replace('/(AUTO_INCREMENT=)([0-9]+)/', '${1}1', $createStatement['create']);
        $createStatement['create'] .= ';';

        if ($addIfNotExists) {
            if ($createStatement['type'] == 'table') {
                /* @noinspection SqlNoDataSourceInspection */
                $createStatement['create'] = preg_replace('/CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS',
                    $createStatement['create']);
            } elseif ($createStatement['type'] == 'view') {
                $createStatement['create'] = preg_replace('/CREATE/', 'CREATE OR REPLACE', $createStatement['create']);
            }
        }

        return $createStatement;
    }

    /**
     * Get all rows from the DB result.
     *
     * @param \mysqli_result $res The database query response
     *
     * @return array The array of SQL rows
     */
    private function getRowsArray($res)
    {
        if (!$res) {
            return [];
        }

        $rows = [];

        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }
}
