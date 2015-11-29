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
namespace Kicaj\Test\SchemaDump\Database\Driver;

use Kicaj\SchemaDump\Database\Driver\MySQL;
use Kicaj\Test\SchemaDump\BaseTest;
use Kicaj\Tools\Helper\Str;

/**
 * Tests for MySQL driver.
 *
 * @coversDefaultClass \Kicaj\SchemaDump\Database\Driver\MySQL
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class MySQL_Test extends BaseTest
{
    protected static $residentFixtures = ['bigtable_create.sql'];

    /**
     * Database driver we are testing.
     *
     * @var MySQL
     */
    protected $driver;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new MySQL();
        $this->driver->dbSetup(self::getDefaultConfig()['connection'])->dbConnect();
    }

    /**
     * @dataProvider connectionProvider
     *
     * @covers ::dbSetup
     * @covers ::dbConnect
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $database
     * @param string $port
     * @param bool   $connect
     * @param string $expMsg
     */
    public function test_connection($host, $username, $password, $database, $port, $connect, $expMsg)
    {
        $this->driver = null;

        // Database config
        $dbConfig = [
            'host' => $host,
            'username' => $username,
            'password' => $password,
            'database' => $database,
            'port' => $port,
        ];

        $myMySQL = new MySQL();
        $connected = $myMySQL->dbSetup($dbConfig)->dbConnect();

        $this->assertSame($connect, $connected);

        $error = $gotMsg = $myMySQL->getError();

        if ($connect) {
            $this->assertNull($error);
        } else {
            $gotMsg = $error->getMessage();
            $this->assertContains($expMsg, $gotMsg);
        }
    }

    public function connectionProvider()
    {
        return [
            [$GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], $GLOBALS['DB_DATABASE'], 3306, true, ''],
            [$GLOBALS['DB_HOST'], $GLOBALS['DB_USERNAME'], $GLOBALS['DB_PASSWORD'], 'not_existing', 3306, false, "'not_existing'"],
        ];
    }

    /**
     * @covers ::dbGetTableNames
     * @covers ::getRowsArray
     */
    public function test_getDbTableNames()
    {
        $tableNames = $this->driver->dbGetTableNames();
        $this->assertSame(['bigtable'], $tableNames);
    }

    /**
     * @covers ::dbGetCreateStatement
     */
    public function test_getDbCreateStatement()
    {
        self::dbDropTable('bigtable');
        self::dbLoadFixture('bigtable_create.sql');

        $expCreate = self::loadFileFixture('bigtable_create.sql');
        $gotCreate = $this->driver->dbGetCreateStatement('bigtable');

        $this->assertSame(2, count($expCreate));
        $expCreate = $expCreate[1]; // Get create statement only

        $this->assertFalse($this->driver->hasError());
        $this->assertSame(4, count(array_keys($gotCreate)));
        $this->assertArrayHasKey('create', $gotCreate);
        $this->assertArrayHasKey('drop', $gotCreate);
        $this->assertArrayHasKey('type', $gotCreate);
        $this->assertArrayHasKey('name', $gotCreate);
        $this->assertSame('bigtable', $gotCreate['name']);

        $expCreate = Str::oneLine($expCreate);
        $gotCreate['create'] = Str::oneLine($gotCreate['create']);

        $this->assertSame($expCreate, $gotCreate['create']);
        $this->assertNotContains('IF NOT EXISTS', $gotCreate['create']);
    }

    /**
     * @covers ::dbGetCreateStatement
     * @covers ::getRowsArray
     */
    public function test_getDbCreateStatement_err()
    {
        $gotCreate = $this->driver->dbGetCreateStatement('notExisting');
        $this->assertSame([], $gotCreate);
    }

    /**
     * @covers ::dbGetCreateStatement
     * @covers ::fixCreateStatement
     */
    public function test_getDbCreateStatement_ifNotExist()
    {
        self::dbDropTable('bigtable');
        self::dbLoadFixture('bigtable_create.sql');

        $gotCreate = $this->driver->dbGetCreateStatement('bigtable', true);
        $gotCreate['create'] = Str::oneLine($gotCreate['create']);

        $this->assertContains('IF NOT EXISTS ', $gotCreate['create']);
    }

    /**
     * @covers ::dbGetCreateStatements
     */
    public function test_getDbCreateStatements()
    {
        self::dbDropAllTables();
        self::dbLoadFixture('bigtable_create.sql');
        self::dbLoadFixture('test1.sql');

        $gotCreates = $this->driver->dbGetCreateStatements();

        $this->assertSame(2, count(array_keys($gotCreates)));
        $this->assertArrayHasKey('bigtable', $gotCreates);
        $this->assertArrayHasKey('test1', $gotCreates);
    }

    /**
     * @covers ::parseIndex
     * @covers ::dbGetCreateStatements
     */
    public function test_getDbCreateStatements_indexes()
    {
        self::dbDropAllTables();
        self::dbLoadFixture('bigtable_create.sql');

        $tableDef = $this->driver->dbGetTableDefinition('bigtable');

        $this->assertSame(4, count($tableDef->getIndexes()));
        $this->assertNotEmpty($tableDef->getPrimaryKey());
    }

    /**
     * @covers ::dbGetTableDropCommand
     */
    public function test_getDbTableDropCommand()
    {
        $exp = 'DROP TABLE IF EXISTS `tableX`;';
        $got = $this->driver->dbGetTableDropCommand('tableX');

        $this->assertSame($exp, $got);
    }

    /**
     * @covers ::dbClose
     */
    public function test_dbClose()
    {
        $closed = $this->driver->dbClose();
        $this->assertTrue($closed);

        $closed = $this->driver->dbClose();
        $this->assertTrue($closed);
    }
}
