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
namespace Kicaj\Schema;

use Kicaj\Tools\Helper\Arr;

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

    /**
     * Schema export configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * Database.
     *
     * @var Db
     */
    private $db;

    /**
     * Constructor.
     *
     * @throws SchemaEx
     *
     * @param array $dbConfig The database configuration.
     */
    public function __construct(array $dbConfig)
    {
        $this->config = $dbConfig;
        $this->db = Db::factory($dbConfig);
    }

    /**
     * Make.
     *
     * @param array $dbConfig The database configuration
     *
     * @throws SchemaEx
     *
     * @return Schema
     */
    public static function make(array $dbConfig): Schema
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
    public static function isValidFormat(string $format): bool
    {
        return in_array($format, [self::FORMAT_PHP_ARRAY, self::FORMAT_PHP_FILE, self::FORMAT_SQL]);
    }

    /**
     * Get create statements string.
     *
     * @param string $format The format of the file: self::FORMAT_*
     *
     * @throws SchemaEx
     *
     * @return array|string
     */
    public function getCreateStatements($format = null)
    {
        $exportType = Arr::get($this->config, self::CONFIG_KEY_EXPORT_FORMAT);

        // Override format from config.
        if ($format != null) {
            $exportType = $format;
        }

        $aine = Arr::get($this->config, self::CONFIG_KEY_AINE, false);

        $createStatements = [];
        $tableNames = array_merge($this->db->dbGetTableNames(), $this->db->dbGetViewNames());
        foreach ($tableNames as $tableName) {
            $table = $this->db->dbGetTableDefinition($tableName);
            $createStatements[$table->getName()] = $table->getCreateStatement($aine);
        }

        $ret = null;

        switch ($exportType) {

            case self::FORMAT_PHP_ARRAY:
                $ret = $createStatements;
                break;

            case self::FORMAT_PHP_FILE:
                $createStatements = array_map(['\Kicaj\Tools\Helper\Str', 'oneLine'], $createStatements);
                $configArray = var_export($createStatements, true);
                $ret = "<?php declare(strict_types=1);\n\n" . '$createStatements = ' . $configArray . ";\n\n";
                $ret .= 'return $createStatements;' . "\n";
                break;

            case self::FORMAT_SQL:
                $createStatements = array_map(['\Kicaj\Tools\Helper\Str', 'oneLine'], array_values($createStatements));
                $ret = implode("\n", array_values($createStatements)) . "\n";
                break;

            default:
                throw new SchemaEx('unknown format: ' . $exportType);
        }

        return $ret;
    }
}
