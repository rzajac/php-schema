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
namespace Kicaj\SchemaDump;

use Kicaj\Tools\Cli\Interaction;
use Kicaj\Tools\Db\DbConnect;
use Kicaj\Tools\Db\DbConnector;
use Kicaj\Tools\Traits\Error;
use Kicaj\Tools\Tst\Tst;

/**
 * Class handles schemadump CLI command.
 *
 * Class handles command line options which are later
 * used by SchemaDump class.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Cli
{
    use Error;

    /**
     * Database host.
     *
     * @var string
     */
    private $dbHost;

    /**
     * Database port.
     *
     * @var string
     */
    private $dbPort = '3306';

    /**
     * Database user name.
     *
     * @var string
     */
    private $dbUser;

    /**
     * Database password.
     *
     * @var string
     */
    private $dbPass;

    /**
     * Database name.
     *
     * @var string
     */
    private $dbName;

    /**
     * Database driver name.
     *
     * @var string
     */
    private $dbDriver = DbConnector::DB_DRIVER_MYSQL;

    /**
     * The path to output file.
     *
     * @var string
     */
    private $outputFile = '';

    /**
     * Add DROP TABLE before each CREATE TABLE.
     *
     * @var bool
     */
    private $dropBeforeCreate = false;

    /**
     * Add IF NOT EXIST to CREATE statements.
     *
     * @var bool
     */
    private $addIfNotExists = true;

    /**
     * Output file format.
     *
     * One of SchemaDump::FORMAT_*
     *
     * @var string
     */
    private $expFormat = SchemaDump::FORMAT_PHP_FILE;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Make.
     *
     * @return self
     */
    public static function make()
    {
        return new self();
    }

    /**
     * Return CLI short options for use in getopt function.
     *
     * @return string
     */
    public static function getCliShortOptions()
    {
        // Command line options
        $cliShortOptionsDef = 'h:'; // Database host
        $cliShortOptionsDef .= 'u:'; // Database user
        $cliShortOptionsDef .= 'p:'; // Database port
        $cliShortOptionsDef .= 'd:'; // Database name
        $cliShortOptionsDef .= 'c:'; // Config file
        $cliShortOptionsDef .= 'o:'; // Output file
        $cliShortOptionsDef .= 'r:'; // Database driver

        return $cliShortOptionsDef;
    }

    /**
     * Return CLI long options for use in getopt function.
     *
     * @return string
     */
    public static function getCliLongOptions()
    {
        return [
            'sql',
            //'drop-before-create:',
            'add-if-not-exists:',
            'help',
        ];
    }

    /**
     * Parse command line or config file options.
     *
     * @param array $options The command line options parsed by getopt
     *
     * @throws \Exception
     *
     * @return bool Returns false on error
     */
    public function setOptions($options)
    {
        if (!is_array($options)) {
            $this->addError('could not parse commend line options');

            return false;
        }

        if (empty($options)) {
            $this->addError('you must pass at least -c option');

            return false;
        }

        if (array_key_exists('help', $options)) {
            $this->addError(self::usage());

            return false;
        }

        if (array_key_exists('c', $options)) {
            $config = $this->getConfigFile($options['c']);
            $options = $this->parseConfigFile($config);
        }

        return $this->validateOptions($options);
    }

    /**
     * Get config file.
     *
     * @param string $configFile The path to configuration file
     *
     * @throws \Exception
     *
     * @return array Returns config options array or empty array on error
     */
    private function getConfigFile($configFile)
    {
        if (is_file($configFile) && is_readable($configFile)) {
            /* @noinspection PhpIncludeInspection */
            return require $configFile;
        }

        $this->addError('config file is not readable');

        return [];
    }

    // @codeCoverageIgnoreStart
    /**
     * Get database password.
     *
     * Note: Will ask user for password
     *
     * @return string
     */
    public function getDbPassword()
    {
        if ($this->dbPass === null) {
            $this->dbPass = trim(Interaction::getPassword('enter database password: '));
            echo "\n";
        }

        return $this->dbPass;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Get database config.
     *
     * @return array
     */
    public function getDbConfig()
    {
        $password = Tst::isUnitTested() ? 'unitTestPass' : $this->getDbPassword();

        $dbConfig = DbConnect::getCfg(
            $this->dbDriver,
            $this->dbHost,
            $this->dbUser,
            $password,
            $this->dbName,
            $this->dbPort);

        return $dbConfig;
    }

    /**
     * Get file format to use.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->expFormat;
    }

    /**
     * Set addIfNotExists property.
     *
     * @return bool
     */
    public function isAddIfNotExists()
    {
        return $this->addIfNotExists;
    }

    public function isDropBeforeCreate()
    {
        return $this->dropBeforeCreate;
    }

    /**
     * Set outputFile property.
     *
     * @return string
     */
    public function getOutputFile()
    {
        return $this->outputFile;
    }

    /**
     * Parse config file values.
     *
     * @param array $config The values from config file
     *
     * @return array The database connection config
     */
    private function parseConfigFile(array $config)
    {
        if (empty($config)) {
            return [];
        }

        $options = [];

        $options['r'] = $config['connection'][DbConnector::DB_CFG_DRIVER];
        $options['h'] = $config['connection'][DbConnector::DB_CFG_HOST];
        $options['u'] = $config['connection'][DbConnector::DB_CFG_USERNAME];
        $this->dbPass = $config['connection'][DbConnector::DB_CFG_PASSWORD];
        $options['d'] = $config['connection'][DbConnector::DB_CFG_DATABASE];

        $this->dbPort = (string) $config['connection'][DbConnector::DB_CFG_PORT];
        $this->expFormat = $config['export_type'];
        $this->outputFile = $config['output_file'];

        $this->addIfNotExists = $config['add_if_not_exists'];
        $this->dropBeforeCreate = $config['drop_before_create'];

        return $options;
    }

    /**
     * Validate command line options.
     *
     * @param array $options The options array
     *
     * @return bool Returns FALSE on error
     */
    private function validateOptions(array $options)
    {
        if (array_key_exists('h', $options)) {
            $this->dbHost = $options['h'];
        } else {
            $this->addError('database host must be provided');

            return false;
        }

        if (array_key_exists('r', $options)) {
            $this->dbDriver = $options['r'];
        }

        if (array_key_exists('u', $options)) {
            $this->dbUser = $options['u'];
        } else {
            $this->addError('database user name must be provided');

            return false;
        }

        if (array_key_exists('d', $options)) {
            $this->dbName = $options['d'];
        } else {
            $this->addError('database name must be provided');

            return false;
        }

        if (array_key_exists('p', $options)) {
            $this->dbPort = (string) $options['p'];
        }

        if (array_key_exists('o', $options)) {
            $this->outputFile = $options['o'];
            if (!$this->setOutputFile($this->outputFile)) {
                return false;
            }
        }

        if (array_key_exists('sql', $options)) {
            $this->expFormat = 'sql';
        }

        if (array_key_exists('drop-before-create', $options)) {
            $this->dropBeforeCreate = $options['drop-before-create'] == 'true' ? true : false;
        }

        if (array_key_exists('add-if-not-exists', $options)) {
            $this->addIfNotExists = $options['add-if-not-exists'] == 'true' ? true : false;
        }

        return true;
    }

    /**
     * Set output file path.
     *
     * @param string $filePath The path to a file to save optput to
     *
     * @return bool
     */
    protected function setOutputFile($filePath)
    {
        $pathInfo = pathinfo($filePath);
        $dir = realpath($pathInfo['dirname']);

        $filePath = $dir.DIRECTORY_SEPARATOR.$pathInfo['basename'];

        if (!is_writable($dir)) {
            return $this->addError("directory $dir is not writable");
        }

        $this->outputFile = $filePath;

        return true;
    }

    /**
     * Command line usage help.
     *
     * @return string
     */
    public static function usage()
    {
        $usage = <<<EOF
Usage: schemadump [options]

Dump MySQL table create statements for all tables in selected database.

Options:
 -h                                : Database host name or IP
 -u                                : Database user name
 -p                                : Database port
 -d                                : Database name
 -r                                : Database driver: mysql
 -c                                : Path to config file. If passed all other options are ignored
 -o                                : Output file
 --sql                             : Dump SQL statements
 --drop-before-create=[true|false] : Add DROP TABLE SQL before each CREATE TABLE statement
 --add-if-not-exists=[true|false]  : Add CREATE IF NOT EXISTS to all CREATE TABLE statements
 --help                            : This help message

Example config file:

<?php
\$config = array
(
    'connection'    => array
    (
        'user'     => 'dbusername',
        'pass'     => 'dbpassword',
        'host'     => '127.0.0.1',
        'port'     => '3306',
        'database' => 'my_database_name'
    ),
    'export_type' => 'phparray',
    'drop_before_create' => TRUE,
    'add_if_not_exists' => TRUE,
    'output_file' => '/tmp/schema.sql'
);
return \$config;

EOF;

        return $usage;
    }
}
