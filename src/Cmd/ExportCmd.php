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

namespace Kicaj\Schema\Cmd;

use Kicaj\Schema\Database\DbConnector;
use Kicaj\Schema\Schema;
use Kicaj\Schema\SchemaEx;
use Kicaj\Tools\Cli\Interaction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Schema export command.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class ExportCmd extends Command
{
    /** The key in config array where absolute path to config is stored. */
    const CONFIG_KEY_PATH = '_config_path_';

    /**
     * Configuration.
     *
     * @var array
     */
    protected $config = [];

    protected function configure()
    {
        $this->setName('export')
             ->setDescription('Export database schema')
             ->addOption(
                 'config',
                 'c',
                 InputOption::VALUE_OPTIONAL,
                 'The path to configuration file. If not set current working directory will be searched for db_config.json');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Kicaj\Schema\SchemaEx
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configPath = $this->getConfigPath($input->getOption('config'));

        $config = $this->readConfigFile($configPath);
        $config = $this->fixOutputFormat($config);
        $this->config = $this->amendAndValidateConfig($config);

        $schema = Schema::make($this->config);
        $createStatements = $schema->getCreateStatements();

        file_put_contents($this->config['output_file'], $createStatements);
    }

    /**
     * Get default config path if not provided as CLI option.
     *
     * @param string $configPath The config path provided on CLI.
     *
     * @return string The path to configuration file.
     */
    public function getConfigPath(string $configPath): string
    {
        return $configPath ?: getcwd() . '/db_config.json';
    }

    /**
     * Read configuration file.
     *
     * Method sets _config_path_ key in config array to $configPath.
     *
     * @param string $configPath The path to JSON configuration file.
     *
     * @throws SchemaEx
     *
     * @return array
     */
    public function readConfigFile(string $configPath): array
    {
        if (!file_exists($configPath)) {
            throw new SchemaEx(sprintf('Config file %s does not exist.', $configPath));
        }


        if (!is_readable($configPath)) {
            throw new SchemaEx(sprintf('Config file %s is not readable.', $configPath));
        }

        $content = file_get_contents($configPath);

        if ('' == $content) {
            throw new SchemaEx(sprintf('Invalid config %s - empty file.', $configPath, json_last_error_msg()));
        }

        $config = json_decode($content, true);
        if ($config === null) {
            throw new SchemaEx(sprintf('Invalid config %s - %s.', $configPath, json_last_error_msg()));
        }

        $config[self::CONFIG_KEY_PATH] = $configPath;

        return $config;
    }

    /**
     * Fix output format.
     *
     * On CLI export as array means PHP file.
     *
     * @param array $config The configuration array.
     *
     * @return array Fixed configuration array.
     */
    public function fixOutputFormat(array $config): array
    {
        if ($config[Schema::CONFIG_KEY_EXPORT_FORMAT] === Schema::FORMAT_PHP_ARRAY) {
            $config[Schema::CONFIG_KEY_EXPORT_FORMAT] = Schema::FORMAT_PHP_FILE;
        }

        return $config;
    }

    /**
     * Amend configuration options.
     *
     * - update to absolute paths
     * - set config_dir
     * - update Schema::CONFIG_KEY_EXPORT_FORMAT if needed
     *
     * @param array $config The configuration array.
     *
     * @throws SchemaEx
     *
     * @return array The amended and validated configuration array.
     */
    public function amendAndValidateConfig(array $config): array
    {
        $configDir = dirname($config[self::CONFIG_KEY_PATH]);
        $outputPath = $config[Schema::CONFIG_KEY_OUTPUT_FILE];
        $config[Schema::CONFIG_KEY_OUTPUT_FILE] = $this->getOutputFilePath($configDir, $outputPath);

        return $config;
    }

    /**
     * Returns path to output file.
     *
     * @param string $configDir     The absolute path where configuration file is.
     * @param string $outputPath    The path where output file should be put. Paths not starting with / are treated
     *                              as relative to config file.
     *
     * @throws SchemaEx When output file cannot be written.
     *
     * @return string
     */
    public function getOutputFilePath(string $configDir, string $outputPath): string
    {
        if ($outputPath[0] !== DIRECTORY_SEPARATOR) {
            $outputPath = $configDir . DIRECTORY_SEPARATOR . $outputPath;
        }

        $outputDir = dirname($outputPath);

        if (!is_dir($outputDir)) {
            throw new SchemaEx(sprintf('Directory %s does not exist.', $outputDir));
        }

        if (!is_writable($outputDir)) {
            throw new SchemaEx(sprintf('Output directory %s is not writable.', $outputDir));
        }

        if (file_exists($outputPath) && !is_writable($outputPath)) {
            throw new SchemaEx(sprintf('Cannot write %s file.', $outputPath));
        }

        return $outputPath;
    }

    /**
     * Get database password.
     *
     * Note: Will ask user for password if database password is not provided in config.
     *
     * @return string
     */
    public function askDbPassword(): string
    {
        return trim(Interaction::getPassword('Enter database password: '));
    }

    /**
     * Get configuration array.
     *
     * @param array $config The Schema configuration array.
     *
     * @return array The configuration array.
     */
    public function checkDbPassword(array $config): array
    {
        $password = $config[Schema::CONFIG_KEY_CONNECTION][DbConnector::DB_CFG_PASSWORD];

        if (!$password) {
            $config[Schema::CONFIG_KEY_CONNECTION][DbConnector::DB_CFG_PASSWORD] = $this->askDbPassword();
        }

        return $config;
    }
}
