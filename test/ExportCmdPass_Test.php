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

namespace Kicaj\Test\Schema {

    use Kicaj\Schema\Cmd\ExportCmd;
    use Kicaj\Schema\Schema;
    use Kicaj\Test\Helper\TestCase\FixtureTestCase;
    use Kicaj\Schema\Database\DbConnector;
    use Symfony\Component\Console\Application;
    use Symfony\Component\Console\Tester\CommandTester;

    /**
     * ExportCommand_Test.
     *
     * @coversDefaultClass \Kicaj\Schema\Cmd\ExportCmd
     *
     * @author Rafal Zajac <rzajac@gmail.com>
     */
    class ExportCommandPass_Test extends FixtureTestCase
    {
        /**
         * @var Application
         */
        protected $app;

        /**
         * @var ExportCmd
         */
        protected $cmd;

        /**
         * @var CommandTester
         */
        protected $cmdTester;

        protected function setUp()
        {
            $this->cmd = new ExportCmd();
            $this->app = new Application();
            $this->app->add($this->cmd);

            global $useMyFgets;
            $useMyFgets = true;
        }

        protected function tearDown()
        {
            global $useMyFgets;
            $useMyFgets = false;
        }

        /**
         * @covers ::checkDbPassword
         *
         * @throws \Kicaj\Test\Helper\Loader\FixtureLoaderEx
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
         * @covers ::checkDbPassword
         * @covers ::askDbPassword
         *
         * @throws \Kicaj\Test\Helper\Loader\FixtureLoaderEx
         */
        public function test_checkDbPassword_missing()
        {
            // Given
            $cfg = self::getFixtureData('db_config.json');
            $cfg[Schema::CONFIG_KEY_CONNECTION][DbConnector::DB_CFG_PASSWORD] = '';

            // When
            $config = $this->cmd->checkDbPassword($cfg);

            // Then
            $this->expectOutputString('Enter database password: ');
            $this->assertSame('testPassABC', $config[Schema::CONFIG_KEY_CONNECTION][DbConnector::DB_CFG_PASSWORD]);
        }
    }
}

// Inject our own fgets function to Kicaj\Tools\Cli.
namespace Kicaj\Tools\Cli {

    /** @noinspection PhpUnusedLocalVariableInspection */
    $useMyFgets = false;

    function fgets($handle)
    {
        global $useMyFgets;

        return $useMyFgets ? 'testPassABC' : \fgets($handle);
    }
}
