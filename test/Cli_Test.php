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
namespace Kicaj\Test\SchemaDump;

use Kicaj\SchemaDump\Cli;
use Kicaj\SchemaDump\SchemaDump;
use Kicaj\Test\Helper\TestCase\FixtureTestCase;
use Kicaj\Tools\Db\DbConnector;

/**
 * Cli class unit tests.
 *
 * @coversDefaultClass Kicaj\SchemaDump\Cli
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Cli_Test extends FixtureTestCase
{
    /**
     * @var Cli
     */
    protected $cli;

    protected function setUp()
    {
        parent::setUp();

        $this->cli = Cli::make();
    }

    /**
     * @covers ::__construct
     * @covers ::make
     */
    public function test___construct()
    {
        $cli = new Cli();
        $this->assertNotNull($cli);

        $cli = Cli::make();
        $this->assertInstanceOf('Kicaj\SchemaDump\Cli', $cli);
    }

    /**
     * @covers ::getCliShortOptions
     */
    public function test_getCliShortOptions()
    {
        $opts = $this->cli->getCliShortOptions();
        $this->assertSame('h:u:p:d:c:o:r:', $opts);
    }

    /**
     * @covers ::getCliLongOptions
     */
    public function test_getCliLongOptions()
    {
        $opts = $this->cli->getCliLongOptions();
        $this->assertSame(['sql', 'add-if-not-exists:', 'help'], $opts);
    }

    /**
     * @dataProvider setOptionsProvider
     *
     * @covers ::setOptions
     * @covers ::getConfigFile
     * @covers ::parseConfigFile
     * @covers ::validateOptions
     * @covers ::usage
     *
     * @param array  $options
     * @param bool   $expRet
     * @param string $expErrorMsg
     */
    public function test_setOptions($options, $expRet, $expErrorMsg)
    {
        $gotRet = $this->cli->setOptions($options);
        $this->assertSame($expRet, $gotRet);

        if ($expErrorMsg) {
            $this->assertSame($expErrorMsg, $this->cli->getError()->getMessage());
        } else {
            $this->assertFalse($this->cli->hasError());
        }
    }

    public function setOptionsProvider()
    {
        return [
            [ null, false, 'could not parse commend line options' ],
            [ [], false, 'you must pass at least -c option' ],
            [ ['help' => true], false, Cli::usage() ],
            [ ['c' => FIXTURE_PATH.'/notThere.php'], false, 'config file is not readable' ],
            [ ['c' => FIXTURE_PATH.'/cfg1.php'], true, '' ],
            [ ['p' => '3306'], false, 'database host must be provided' ],
            [ ['h' => '127.0.0.1'], false, 'database user name must be provided' ],
            [ ['h' => '127.0.0.1', 'u' => 'user'], false, 'database name must be provided' ],
            [ ['h' => '127.0.0.1', 'u' => 'user', 'd' => 'dbName'], true, '' ],
        ];
    }

    /**
     * @dataProvider setOptionsProvider2
     *
     * @covers ::setOptions
     * @covers ::validateOptions
     *
     * @param array $options
     * @param string $key
     * @param mixed $expValue
     */
    public function test_setOptions2($options, $key, $expValue)
    {
        $this->cli->setOptions($options);
        $gotDbConfig = $this->cli->getDbConfig();

        $this->assertArrayHasKey($key, $gotDbConfig);
        $this->assertSame($expValue, $gotDbConfig[$key]);
    }

    public function setOptionsProvider2()
    {
        return [
            [ ['h' => '127.0.0.1', 'u' => 'user', 'd' => 'dbName'], DbConnector::DB_CFG_PORT, '3306' ],
            [ ['h' => '127.0.0.1', 'u' => 'user', 'd' => 'dbName', 'p' => '1234'], DbConnector::DB_CFG_PORT, '1234' ],
        ];
    }

    /**
     * @covers ::getFormat
     * @covers ::validateOptions
     */
    public function test_getFormat()
    {
        $options = ['h' => '127.0.0.1', 'u' => 'user', 'd' => 'dbName', 'p' => '1234', 'sql' => true];

        $this->assertSame(SchemaDump::FORMAT_PHP_FILE, $this->cli->getFormat());
        $this->cli->setOptions($options);
        $this->assertSame(SchemaDump::FORMAT_SQL, $this->cli->getFormat());

    }

    /**
     * @covers ::isDropBeforeCreate
     * @covers ::validateOptions
     */
    public function test_isDropBeforeCreate()
    {
        $options = [
            'h' => '127.0.0.1',
            'u' => 'user',
            'd' => 'dbName',
            'p' => '1234',
            'sql' => true,
            'drop-before-create' => 'true'];

        $this->assertFalse($this->cli->isDropBeforeCreate());
        $this->cli->setOptions($options);
        $this->assertTrue($this->cli->isDropBeforeCreate());
    }

    /**
     * @covers ::isAddIfNotExists
     * @covers ::validateOptions
     */
    public function test_isAddIfNotExists()
    {
        $options = [
            'h' => '127.0.0.1',
            'u' => 'user',
            'd' => 'dbName',
            'p' => '1234',
            'sql' => true,
            'drop-before-create' => 'true',
            'add-if-not-exists' => 'false'];

        $this->assertTrue($this->cli->isAddIfNotExists());
        $this->cli->setOptions($options);
        $this->assertFalse($this->cli->isAddIfNotExists());
    }

    /**
     * @covers ::getOutputFile
     * @covers ::validateOptions
     * @covers ::setOutputFile
     */
    public function test_getOutputFile()
    {
        $options = [
            'h' => '127.0.0.1',
            'u' => 'user',
            'd' => 'dbName',
            'p' => '1234',
            'sql' => true,
            'drop-before-create' => 'true',
            'o' => FIXTURE_PATH.'/test_output'];

        $this->assertSame('', $this->cli->getOutputFile());
        $this->cli->setOptions($options);
        $this->assertSame(FIXTURE_PATH.'/test_output', $this->cli->getOutputFile());
    }

    /**
     * @covers ::getDbConfig
     * @covers ::getFormat
     * @covers ::getOutputFile
     * @covers ::parseConfigFile
     * @covers ::setOptions
     * @covers ::validateOptions
     */
    public function test_getDbConfig()
    {
        $expOptions = $this->loadFileFixture('cfg1.php');

        $dbOptions = ['c' => FIXTURE_PATH.'/cfg1.php'];
        $this->cli->setOptions($dbOptions);

        $this->assertSame($expOptions['connection'], $this->cli->getDbConfig());
        $this->assertSame($expOptions['export_type'], $this->cli->getFormat());
        $this->assertSame($expOptions['output_file'], $this->cli->getOutputFile());
    }
}
