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
use Kicaj\Tools\Db\DbConnector;
use Kicaj\Tools\Exception;
use Kicaj\Tools\Tst\Tst;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dump command.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class DumpCommand extends Command
{
    /**
     * The path where configure file was loaded from.
     *
     * @var string
     */
    protected $configDir;

    /**
     * Configuration.
     *
     * @var array
     */
    protected $config = [];

    protected function configure()
    {
        // Set default configuration
        $this->config = [
            'connection' => [
                DbConnector::DB_CFG_USERNAME => 'testUser',
                DbConnector::DB_CFG_PASSWORD => 'testUserPass',
                DbConnector::DB_CFG_HOST => 'localhost',
                DbConnector::DB_CFG_PORT => '3306',
                DbConnector::DB_CFG_DATABASE => 'test',
            ],
            DbConnector::DB_CFG_DRIVER => DbConnector::DB_DRIVER_MYSQL,
            'export_type' => SchemaDump::FORMAT_PHP_ARRAY,
            'drop_before_create' => true,
            'add_if_not_exists' => true,
            'output_file' => 'tmp/schema.php',
        ];

        $this->setName('dump')
             ->setDescription('Dump database schema')
             ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The path to configuration JSON file. If not set it will search for db_config.json in current directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $input->getOption('config');

        if ($configPath === null) {
            $configPath = getcwd().'/db_config.json';
        }

        $this->configDir = realpath(dirname($configPath));

        if (!(file_exists($configPath) && is_readable($configPath))) {
            throw new Exception(sprintf('file %s is not readable', $configPath));
        }

        $this->config = array_merge($this->config, json_decode(file_get_contents($configPath), true));

        if ($this->config === null) {
            throw new Exception(sprintf('invalid %s - %s', $configPath, json_last_error_msg()));
        }

        $this->parseConfig();

        $schemaDump = SchemaDump::make($this->getDbConfig());
        if ($schemaDump->hasErrors()) {
            throw new Exception($schemaDump->getError()->getMessage());
        }

        $exportType = null;
        if ($this->config['export_type'] == SchemaDump::FORMAT_PHP_ARRAY) {
            $exportType = SchemaDump::FORMAT_PHP_FILE;
        }

        $createStatements = $schemaDump->getCreateStatements($exportType);

        file_put_contents($this->config['output_file'], $createStatements);
    }

    protected function parseConfig()
    {
        $this->config['output_file'] = $this->getPath($this->config['output_file']);
        $dir = dirname($this->config['output_file']);
        if (!is_writable($dir)) {
            throw new Exception(sprintf('directory is not writable: %s', $dir));
        }
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
        $password = $this->config['connection'][DbConnector::DB_CFG_PASSWORD];

        if (!$password) {
            $this->config['connection'][DbConnector::DB_CFG_PASSWORD] = trim(Interaction::getPassword('enter database password: '));
            echo "\n";
        }

        return $password;
    }
    // @codeCoverageIgnoreEnd

    /**
     * Get configuration array.
     *
     * @return array
     */
    public function getDbConfig()
    {
        $password = Tst::isUnitTested() ? 'unitTestPass' : $this->getDbPassword();
        $this->config['connection'][DbConnector::DB_CFG_PASSWORD] = $password;

        return $this->config;
    }

    /**
     * Get path from relative path.
     *
     * @param string $path The path relative to configuration JSON file
     *
     * @return string
     */
    public function getPath($path)
    {
        if ($path[0] != '/') {
            $path = '/'.$path;
        }

        return $this->configDir.$path;
    }
}
