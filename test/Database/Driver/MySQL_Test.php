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
use Kicaj\Tools\Db\DatabaseException;
use Kicaj\Tools\Db\DbConnector;
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
    /**
     * Database driver we are testing.
     *
     * @var MySQL
     */
    protected $driver;

    public function setUp()
    {
        $this->driver = new MySQL();
        $this->driver->dbSetup(self::dbGetConfig('SCHEMA_DUMP1'))->dbConnect();
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
            DbConnector::DB_CFG_HOST     => $host,
            DbConnector::DB_CFG_USERNAME => $username,
            DbConnector::DB_CFG_PASSWORD => $password,
            DbConnector::DB_CFG_DATABASE => $database,
            DbConnector::DB_CFG_PORT     => $port,
        ];

        $myMySQL = new MySQL();

        try {
            $thrown = false;
            $myMySQL->dbSetup($dbConfig)->dbConnect();
            $myMySQL->dbGetTableNames(); // Call method that is actually doing something with database.
        } catch (DatabaseException $e) {
            $thrown = true;

            if ($expMsg === '') {
                $this->fail('Did not expect to see error: ' . $e->getMessage());
            }
        } finally {
            if ($expMsg !== '' && $thrown === false) {
                $this->fail('Expected to see error: ' . $expMsg);
            }
        }
    }

    public function connectionProvider()
    {
        return [
            [
                $GLOBALS['TEST_DB_SCHEMA_DUMP1_HOST'],
                $GLOBALS['TEST_DB_SCHEMA_DUMP1_USERNAME'],
                $GLOBALS['TEST_DB_SCHEMA_DUMP1_PASSWORD'],
                $GLOBALS['TEST_DB_SCHEMA_DUMP1_DATABASE'],
                3306,
                true,
                '',
            ],
            [
                $GLOBALS['TEST_DB_SCHEMA_DUMP1_HOST'],
                $GLOBALS['TEST_DB_SCHEMA_DUMP1_USERNAME'],
                $GLOBALS['TEST_DB_SCHEMA_DUMP1_PASSWORD'],
                'not_existing',
                3306,
                false,
                "'not_existing'",
            ],
        ];
    }

    /**
     * @covers ::dbGetTableNames
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
        self::dbDropTables('SCHEMA_DUMP1', 'bigtable');
        self::dbLoadFixtures('SCHEMA_DUMP1', 'bigtable_create.sql');

        $expCreate = self::getFixtureData('bigtable_create.sql');
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
        self::dbDropTables('SCHEMA_DUMP1', 'bigtable');
        self::dbLoadFixtures('SCHEMA_DUMP1', 'bigtable_create.sql');

        $gotCreate = $this->driver->dbGetCreateStatement('bigtable', true);
        $gotCreate['create'] = Str::oneLine($gotCreate['create']);

        $this->assertContains('IF NOT EXISTS ', $gotCreate['create']);
    }

    /**
     * @covers ::dbGetCreateStatements
     */
    public function test_getDbCreateStatements()
    {
        self::dbDropAllTables('SCHEMA_DUMP1');
        self::dbLoadFixtures('SCHEMA_DUMP1', ['bigtable_create.sql', 'test1.sql']);

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
        self::dbDropAllTables('SCHEMA_DUMP1');
        self::dbLoadFixtures('SCHEMA_DUMP1', ['bigtable_create.sql', 'test1.sql']);

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
