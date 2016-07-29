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

namespace Kicaj\Test\Schema;

use Kicaj\Schema\ExportCommand;
use Kicaj\Schema\Schema;
use Kicaj\DbKit\DbConnector;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * ExportCommand_Test.
 *
 * @coversDefaultClass \Kicaj\Schema\ExportCommand
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class ExportCommand_Test extends BaseTest
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ExportCommand
     */
    protected $cmd;

    /**
     * @var vfsStreamDirectory
     */
    protected $vfsRoot;

    protected function setUp()
    {
        $this->cmd = new ExportCommand();
        $this->app = new Application();
        $this->app->add($this->cmd);

        // Virtual filesystem root.
        $this->vfsRoot = vfsStream::setup();
    }

    /**
     * @covers ::configure
     * @covers ::getName
     */
    public function test_configure_name()
    {
        $this->assertSame('export', $this->cmd->getName());
    }

    /**
     * @covers ::configure
     */
    public function test_configure_description()
    {
        // When
        $desc = $this->cmd->getDescription();

        // Then
        $this->assertSame('Export database schema', $desc);
    }

    /**
     * @dataProvider getDefaultConfigPathProvider
     *
     * @covers ::getConfigPath
     *
     * @param string $configPath
     * @param string $expected
     */
    public function test_getDefaultConfigPath($configPath, $expected)
    {
        // When
        $gotPath = $this->cmd->getConfigPath($configPath);

        // Then
        $this->assertSame($expected, $gotPath);
    }

    public function getDefaultConfigPathProvider()
    {
        return [
            ['', getcwd() . '/db_config.json'],
            [false, getcwd() . '/db_config.json'],
            [null, getcwd() . '/db_config.json'],
            ['aaa/bbb/db_config.json', 'aaa/bbb/db_config.json'],
        ];
    }

    /**
     * @covers ::readConfigFile
     *
     * @expectedException \Kicaj\Tools\Exception
     * @expectedExceptionMessage Config file vfs://root/db_config.json does not exist.
     */
    public function test_readConfigFile_notExisting()
    {
        $this->cmd->readConfigFile($this->vfsRoot->url() . '/db_config.json');
    }

    /**
     * @covers ::readConfigFile
     *
     * @expectedException \Kicaj\Tools\Exception
     * @expectedExceptionMessage Config file vfs://root/db_config.json is not readable.
     */
    public function test_readConfigFile_notReadable()
    {
        // Given
        $vFile = vfsStream::newFile('db_config.json', 0000)->at($this->vfsRoot);

        // Then
        $this->cmd->readConfigFile($vFile->url());
    }

    /**
     * @covers ::readConfigFile
     */
    public function test_readConfigFile()
    {
        // Given
        $cfgFile = vfsStream::newFile('db_config.json')
                         ->withContent(self::getFixtureRawData('db_config.json'))
                         ->at($this->vfsRoot)->url();

        // When
        $config = $this->cmd->readConfigFile($cfgFile);

        // Then
        $this->assertSame(5, count($config));
        $this->assertArrayHasKey(Schema::CONFIG_KEY_CONNECTION, $config);
        $this->assertArrayHasKey(Schema::CONFIG_KEY_EXPORT_FORMAT, $config);
        $this->assertArrayHasKey(Schema::CONFIG_KEY_AINE, $config);
        $this->assertArrayHasKey(Schema::CONFIG_KEY_OUTPUT_FILE, $config);
        $this->assertArrayHasKey(ExportCommand::CONFIG_KEY_PATH, $config);

        $this->assertSame(6, count($config[Schema::CONFIG_KEY_CONNECTION]));
        $this->assertArrayHasKey(DbConnector::DB_CFG_HOST, $config[Schema::CONFIG_KEY_CONNECTION]);
        $this->assertArrayHasKey(DbConnector::DB_CFG_USERNAME, $config[Schema::CONFIG_KEY_CONNECTION]);
        $this->assertArrayHasKey(DbConnector::DB_CFG_PASSWORD, $config[Schema::CONFIG_KEY_CONNECTION]);
        $this->assertArrayHasKey(DbConnector::DB_CFG_PORT, $config[Schema::CONFIG_KEY_CONNECTION]);
        $this->assertArrayHasKey(DbConnector::DB_CFG_DATABASE, $config[Schema::CONFIG_KEY_CONNECTION]);
        $this->assertArrayHasKey(DbConnector::DB_CFG_DRIVER, $config[Schema::CONFIG_KEY_CONNECTION]);
    }

    /**
     * @covers ::readConfigFile
     *
     * @expectedException \Kicaj\Tools\Exception
     * @expectedExceptionMessage Invalid config vfs://root/db_config.json - Syntax error.
     */
    public function test_readConfigFile_empty()
    {
        // Given
        $cfgFile = vfsStream::newFile('db_config.json')
                         ->withContent(self::getFixtureRawData('db_config_empty.json'))
                         ->at($this->vfsRoot)->url();

        // Then
        $this->cmd->readConfigFile($cfgFile);
    }

    /**
     * @covers ::readConfigFile
     *
     * @expectedException \Kicaj\Tools\Exception
     * @expectedExceptionMessage Invalid config vfs://root/db_config.json - Syntax error.
     */
    public function test_readConfigFile_syntaxError()
    {
        // Given
        $cfgFile = vfsStream::newFile('db_config.json')
                         ->withContent(self::getFixtureRawData('db_config_syntaxError.json'))
                         ->at($this->vfsRoot)->url();

        // Then
        $this->cmd->readConfigFile($cfgFile);
    }

    /**
     * @covers ::fixOutputFormat
     */
    public function test_fixOutputFormat()
    {
        // Given
        $config = $this->getSchemaConfig('SCHEMA1');

        // When
        $config = $this->cmd->fixOutputFormat($config);

        // Then
        $this->assertSame(Schema::FORMAT_PHP_FILE, $config[Schema::CONFIG_KEY_EXPORT_FORMAT]);
    }

    /**
     * @covers ::amendAndValidateConfig
     */
    public function test_amendAndValidateConfig()
    {
        // Given
        vfsStream::newDirectory('tmp')->at($this->vfsRoot);
        $cfgFile = vfsStream::newFile('db_config.json')
                         ->withContent(self::getFixtureRawData('db_config.json'))
                         ->at($this->vfsRoot)->url();
        $config = $this->cmd->readConfigFile($cfgFile);

        // When
        $configGot = $this->cmd->amendAndValidateConfig($config);

        // Then
        $this->assertSame('tmp/schema.php', $config[Schema::CONFIG_KEY_OUTPUT_FILE]);
        $this->assertSame('vfs://root/tmp/schema.php', $configGot[Schema::CONFIG_KEY_OUTPUT_FILE]);
    }

    /**
     * @covers ::getOutputFilePath
     */
    public function test_getOutputFilePath_ok()
    {
        // Given
        $vCfg = vfsStream::newDirectory('cfg')->at($this->vfsRoot);
        vfsStream::newDirectory('tmp')->at($vCfg);
        $cfgFile = vfsStream::newFile('db_config.json')
                            ->withContent(self::getFixtureRawData('db_config.json'))
                            ->at($vCfg)->url();

        $configDir = dirname($cfgFile);

        // When
        $gotPath = $this->cmd->getOutputFilePath($configDir, 'tmp/schema.php');

        // Then
        $this->assertSame('vfs://root/cfg/tmp/schema.php', $gotPath);
    }

    /**
     * @covers ::getOutputFilePath
     *
     * @expectedException \Kicaj\Tools\Exception
     * @expectedExceptionMessage Output directory vfs://root/cfg/tmp is not writable.
     */
    public function test_getOutputFilePath_outputDirNotWritable()
    {
        // Given
        $vCfg = vfsStream::newDirectory('cfg')->at($this->vfsRoot);
        vfsStream::newDirectory('tmp', 0000)->at($vCfg);
        $cfgFile = vfsStream::newFile('db_config.json')
                          ->withContent(self::getFixtureRawData('db_config.json'))
                          ->at($vCfg)->url();

        $configDir = dirname($cfgFile);

        // When
        $gotPath = $this->cmd->getOutputFilePath($configDir, 'tmp/schema.php');

        // Then
        $this->assertSame('vfs://root/cfg/tmp/schema.php', $gotPath);
    }

    /**
     * @covers ::getOutputFilePath
     *
     * @expectedException \Kicaj\Tools\Exception
     * @expectedExceptionMessage Directory vfs://root/cfg/tmp does not exist.
     */
    public function test_getOutputFilePath_outputDirDoesNotExist()
    {
        // Given
        $vCfg = vfsStream::newDirectory('cfg')->at($this->vfsRoot);
        $cfgFile = vfsStream::newFile('db_config.json')
                          ->withContent(self::getFixtureRawData('db_config.json'))
                          ->at($vCfg)->url();

        $configDir = dirname($cfgFile);

        // When
        $gotPath = $this->cmd->getOutputFilePath($configDir, 'tmp/schema.php');

        // Then
        $this->assertSame('vfs://root/cfg/tmp/schema.php', $gotPath);
    }

    /**
     * @covers ::getOutputFilePath
     *
     * @expectedException \Kicaj\Tools\Exception
     * @expectedExceptionMessage Cannot write vfs://root/cfg/tmp/schema.php file.
     */
    public function test_getOutputFilePath_outputFileNotWritable()
    {
        // Given
        $vCfg = vfsStream::newDirectory('cfg')->at($this->vfsRoot);
        $vTmp = vfsStream::newDirectory('tmp')->at($vCfg);
        vfsStream::newFile('schema.php', 0000)->at($vTmp);
        $cfgFile = vfsStream::newFile('db_config.json')
                          ->withContent(self::getFixtureRawData('db_config.json'))
                          ->at($vCfg)->url();

        $configDir = dirname($cfgFile);

        // When
        $gotPath = $this->cmd->getOutputFilePath($configDir, 'tmp/schema.php');

        // Then
        $this->assertSame('vfs://root/cfg/tmp/schema.php', $gotPath);
    }

    /**
     * @covers ::checkDbPassword
     */
    public function test_checkDbPassword()
    {
        // Given
        $cfg = self::getFixtureData('db_config.json');

        // When
        $config = $this->cmd->checkDbPassword($cfg);

        // Then
        $this->assertSame('testUserPass', $config[Schema::CONFIG_KEY_CONNECTION][DbConnector::DB_CFG_PASSWORD]);
    }

    /**
     * @covers ::execute
     */
    public function test_execute_php()
    {
        // Given
        $this->dbDropAllTables('SCHEMA1');
        $this->dbLoadFixtures('SCHEMA1', 'bigtable_create.sql');

        $vCfg = vfsStream::newDirectory('cfg')->at($this->vfsRoot);
        $vTmp = vfsStream::newDirectory('tmp')->at($vCfg);
        $vSchema = vfsStream::newFile('schema.php')->at($vTmp);
        $cfgFile = vfsStream::newFile('db_config.json')
                            ->withContent(self::getFixtureRawData('db_config.json'))
                            ->at($vCfg)->url();
        $cmd = new CommandTester($this->cmd);

        // When
        $cmd->execute([
            'command' => $this->cmd->getName(),
            '--config' => $cfgFile
        ]);
        $expFile = $this->getFixtureRawData('bigtable-schema-dump.php');
        $gotFile = file_get_contents($vSchema->url());

        // Then
        $this->assertSame($expFile, $gotFile);
    }

    /**
     * @covers ::execute
     */
    public function test_execute_sql()
    {
        // Given
        $this->dbDropAllTables('SCHEMA1');
        $this->dbLoadFixtures('SCHEMA1', 'bigtable_create.sql');

        $vCfg = vfsStream::newDirectory('cfg')->at($this->vfsRoot);
        $vTmp = vfsStream::newDirectory('tmp')->at($vCfg);
        $vSchema = vfsStream::newFile('schema.sql')->at($vTmp);
        $cfgFile = vfsStream::newFile('db_config.json')
                            ->withContent(self::getFixtureRawData('db_config_sql.json'))
                            ->at($vCfg)->url();
        $cmd = new CommandTester($this->cmd);

        // When
        $cmd->execute([
            'command' => $this->cmd->getName(),
            '--config' => $cfgFile
        ]);
        $expFile = $this->getFixtureRawData('bigtable-schema-dump.sql');
        $gotFile = file_get_contents($vSchema->url());

        // Then
        $this->assertSame($expFile, $gotFile);
    }
}
