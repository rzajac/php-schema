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

use Kicaj\Schema\Database\SchemaFactory;
use Kicaj\Tools\Db\DatabaseException;

/**
 * Schema.
 *
 * Exports MySQL database tables CREATE statements to a file.
 *
 * There are two export modes:
 *  - as PHP array      - creates includable PHP file with $createStatements associative array
 *                        where keys are table names and values are SQL CREATE statements.
 *  - as SQL statements - creates file with CREATE statements for all tables in given database.
 *
 * This tool not only exports CREATE statements but rewrites it in following way:
 *  - resets AUTO_INCREMENT to 1
 *  - adds CREATE TABLE IF NOT EXISTS (configurable)
 *  - adds DROP TABLE IF EXISTS (configurable)
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Schema
{
    /** Schema returned as a string which is valid PHP file. */
    const FORMAT_PHP_FILE = 'php_file';

    /** Schema returned as PHP array. */
    const FORMAT_PHP_ARRAY = 'php_array';

    /** Schema returned as SQL. */
    const FORMAT_SQL = 'sql';

    /** Configuration key for database connection configuration. */
    const CONFIG_KEY_CONNECTION = 'connection';
    /** Configuration key for export type. */
    const CONFIG_KEY_EXPORT_FORMAT = 'export_format';
    /** Configuration key for add is not exist. */
    const CONFIG_KEY_AINE = 'add_if_not_exists';
    /** Configuration key for output file path. */
    const CONFIG_KEY_OUTPUT_FILE = 'output_file';

    /** PHP types. */
    const PHP_TYPE_INT = 'int';
    const PHP_TYPE_STRING = 'string';
    const PHP_TYPE_FLOAT = 'float';
    const PHP_TYPE_BOOL = 'bool';
    const PHP_TYPE_BINARY = 'binary';
    const PHP_TYPE_ARRAY = 'array';
    const PHP_TYPE_DATE = 'date';
    const PHP_TYPE_DATETIME = 'datetime';
    const PHP_TYPE_TIMESTAMP = 'timestamp';
    const PHP_TYPE_TIME = 'time';
    const PHP_TYPE_YEAR = 'year';

    const COLUMN_UNSIGNED = 'unsigned';
    const COLUMN_NOT_NULL = 'not null';
    const COLUMN_AUTOINCREMENT = 'autoincrement';

    /**
     * Schema export configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Database driver.
     *
     * @var SchemaGetter
     */
    private $dbDrv;

    /**
     * Constructor.
     *
     * @throws SchemaException
     * @throws DatabaseException
     *
     * @param array $dbConfig The database configuration.
     */
    public function __construct(array $dbConfig)
    {
        $this->config = $dbConfig;
        $this->dbDrv = SchemaFactory::factory($dbConfig, true);
    }

    /**
     * Make.
     *
     * @param array $dbConfig The database configuration
     *
     * @throws SchemaException
     * @throws DatabaseException
     *
     * @return Schema
     */
    public static function make(array $dbConfig)
    {
        return new static($dbConfig);
    }

    /**
     * Returns true for valid format.
     *
     * @param string $format The format of the file: self::FORMAT_*
     *
     * @return bool
     */
    public static function isValidFormat($format)
    {
        return in_array($format, [self::FORMAT_PHP_ARRAY, self::FORMAT_PHP_FILE, self::FORMAT_SQL]);
    }

    /**
     * Get create statements string.
     *
     * @param string $format The format of the file: self::FORMAT_*
     *
     * When returning array the keys are table names and values are an
     * array with keys:
     *
     *   - create - CREATE TABLE or VIEW statement
     *   - drop   - DROP TABLE statement
     *   - type   - table, view ( one of the self::CREATE_TYPE_* )
     *   - name   - table name
     *
     * @throws SchemaException
     *
     * @return array|string
     */
    public function getCreateStatements($format = null)
    {
        $exportType = $this->config[self::CONFIG_KEY_EXPORT_FORMAT];
        if ($format != null) {
            $exportType = $format;
        }

        $createStatements = $this->dbDrv->dbGetCreateStatements($this->config[self::CONFIG_KEY_AINE]);

        $ret = null;

        switch ($exportType) {

            case self::FORMAT_PHP_ARRAY:
                $ret = $createStatements;
                break;

            case self::FORMAT_PHP_FILE:
                $configArray = var_export($createStatements, true);
                $ret = "<?php\n\n" . '$createStatements = ' . $configArray . ";\n\n";
                $ret .= 'return $createStatements;' . "\n";
                break;

            case self::FORMAT_SQL:
                $ret = implode("\n\n", array_values($createStatements)) . "\n";
                break;

            default:
                throw new SchemaException('unknown format: ' . $exportType);
        }

        return $ret;
    }
}
