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
namespace Kicaj\Test\Schema\Database\Driver;

use Kicaj\Schema\Database\DbConnector;
use Kicaj\Schema\Database\MySQL\MySQL;
use Kicaj\Schema\Itf\TableItf;
use Kicaj\Schema\SchemaEx;
use Kicaj\Test\Schema\BaseTest;

/**
 * Tests for MySQL driver.
 *
 * @coversDefaultClass \Kicaj\Schema\Database\MySQL\MySQL
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

    /**
     * @throws \Kicaj\Schema\Database\DbEx
     */
    public function setUp()
    {
        $this->driver = new MySQL();
        $this->driver->dbSetup(self::dbGetConfig('SCHEMA1'))->dbConnect();
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
     * @param string $errorMsg
     */
    public function test_connection($host, $username, $password, $database, $port, $errorMsg)
    {
        // Given
        $this->driver = null;
        $thrown = false;

        // Database config
        $dbConfig = [
            DbConnector::DB_CFG_HOST     => $host,
            DbConnector::DB_CFG_USERNAME => $username,
            DbConnector::DB_CFG_PASSWORD => $password,
            DbConnector::DB_CFG_DATABASE => $database,
            DbConnector::DB_CFG_PORT     => $port,
        ];

        // When
        $myMySQL = new MySQL();

        // Then
        try {
            $myMySQL->dbSetup($dbConfig)->dbConnect();
            // Call method that is actually doing something with database.
            $myMySQL->dbGetTableNames();
        } catch (SchemaEx $e) {
            $thrown = true;
            $this->assertFalse('' == $errorMsg, 'Did not expect to see error: ' . $e->getMessage());
        } finally {
            $this->assertFalse('' !== $errorMsg && false === $thrown, 'Expected to see error: ' . $errorMsg);
        }
    }

    public function connectionProvider()
    {
        return [
            // 0 --------------------------------------------------------------------
            [
                $GLOBALS['TEST_DB_SCHEMA1_HOST'],
                $GLOBALS['TEST_DB_SCHEMA1_USERNAME'],
                $GLOBALS['TEST_DB_SCHEMA1_PASSWORD'],
                $GLOBALS['TEST_DB_SCHEMA1_DATABASE'],
                3306,
                '',
            ],
            // 1 --------------------------------------------------------------------
            [
                $GLOBALS['TEST_DB_SCHEMA1_HOST'],
                $GLOBALS['TEST_DB_SCHEMA1_USERNAME'],
                $GLOBALS['TEST_DB_SCHEMA1_PASSWORD'],
                'not_existing',
                3306,
                "'not_existing'",
            ],
            // XX --------------------------------------------------------------------
        ];
    }

    /**
     * @covers ::dbGetTableNames
     * @covers ::getTableAndViewNames
     * @covers ::runQuery
     * @covers ::getRowsArray
     *
     * @throws \Kicaj\Test\Helper\Database\DatabaseEx
     * @throws \Kicaj\Test\Helper\Loader\FixtureLoaderEx
     * @throws SchemaEx
     */
    public function test_getDbTableNames()
    {
        // Given
        $this->dbDropAllTables('SCHEMA1');
        $this->dbLoadFixtures('SCHEMA1', ['table2.sql', 'table3.sql']);

        // When
        $tableNames = $this->driver->dbGetTableNames();

        // Then
        $this->assertSame(['table2', 'table3'], $tableNames);
    }

    /**
     * @covers ::dbGetViewNames
     * @covers ::getTableAndViewNames
     * @covers ::runQuery
     * @covers ::getRowsArray
     *
     * @throws \Kicaj\Test\Helper\Database\DatabaseEx
     * @throws \Kicaj\Test\Helper\Loader\FixtureLoaderEx
     * @throws SchemaEx
     */
    public function test_dbGetViewNames()
    {
        // Given
        $this->dbDropAllTables('SCHEMA1');
        $this->dbDropAllViews('SCHEMA1');
        $this->dbLoadFixtures('SCHEMA1', ['test1.sql', 'view.sql']);

        // When
        $viewNames = $this->driver->dbGetViewNames();

        // Then
        $this->assertSame(['my_view'], $viewNames);
    }

    /**
     * @covers ::dbGetTableDefinition
     * @covers ::runQuery
     * @covers ::getRowsArray
     *
     * @throws \Kicaj\Test\Helper\Database\DatabaseEx
     * @throws \Kicaj\Test\Helper\Loader\FixtureLoaderEx
     * @throws SchemaEx
     */
    public function test_dbGetTableDefinition_table()
    {
        // Given
        $this->dbDropTables('SCHEMA1', 'bigtable');
        $this->dbLoadFixtures('SCHEMA1', 'bigtable_create.sql');

        // When
        $expCreate = $this->getFixtureRawData('bigtable_create.sql');
        $gotCreate = $this->driver->dbGetTableDefinition('bigtable');

        // Then
        $this->assertSame(TableItf::TYPE_TABLE, $gotCreate->getType());
        $this->assertSame($expCreate, $gotCreate->getCreateStatement());
    }

    /**
     * @covers ::dbGetTableDefinition
     * @covers ::runQuery
     * @covers ::getRowsArray
     *
     * @throws \Kicaj\Test\Helper\Database\DatabaseEx
     * @throws \Kicaj\Test\Helper\Loader\FixtureLoaderEx
     * @throws SchemaEx
     */
    public function test_dbGetTableDefinition_view()
    {
        // Given
        $this->dbDropAllTables('SCHEMA1');
        $this->dbDropAllViews('SCHEMA1');

        $this->dbLoadFixtures('SCHEMA1', ['test1.sql', 'view.sql']);

        // When
        $expCreate = $this->getFixtureRawData('view_fixed.sql');
        $gotCreate = $this->driver->dbGetTableDefinition('my_view');

        // Then
        $this->assertSame(TableItf::TYPE_VIEW, $gotCreate->getType());
        $this->assertSame($expCreate, $gotCreate->getCreateStatement());
    }

    /**
     * @covers ::dbGetTableDefinition
     *
     * @expectedException \Kicaj\Schema\SchemaEx
     * @expectedExceptionMessage doesn't exist
     */
    public function test_dbGetTableDefinition_err()
    {
        $this->driver->dbGetTableDefinition('notExisting');
    }

    /**
     * @covers ::dbGetTableDefinition
     *
     * @expectedException \Kicaj\Schema\SchemaEx
     * @expectedExceptionMessage Was not able to figure out create statement for: test1
     *
     * @throws \Kicaj\Test\Helper\Database\DatabaseEx
     * @throws \Kicaj\Test\Helper\Loader\FixtureLoaderEx
     */
    public function test_dbGetTableDefinition_unknownType()
    {
        // Given
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', ['test1.sql']);

        // When
        $driver = $this->getMockBuilder(MySQL::class)->setMethods(['getRowsArray'])->getMock();
        $driver->method('getRowsArray')->willReturn(['popped', ['some type' => 'not important']]);
        /** @var MySQL $driver */
        $driver->dbSetup(self::dbGetConfig('SCHEMA1'))->dbConnect();

        // Then
        $driver->dbGetTableDefinition('test1');
    }

    /**
     * @covers ::getRowsArray
     */
    public function test_getRowsArray()
    {
        // When
        $got = $this->driver->getRowsArray(false);

        // Then
        $this->assertSame([], $got);
    }

    /**
     * @covers ::runQuery
     *
     * @expectedException \Kicaj\Schema\SchemaEx
     */
    public function test_runQuery_error()
    {
        $this->driver->runQuery('NOT SQL');
    }

    /**
     * @covers ::dbClose
     *
     * @throws \Kicaj\Schema\Database\DbEx
     */
    public function test_dbClose_closingTwice()
    {
        $this->driver->dbClose();
        $this->driver->dbClose();

        // To satisfy PHPUnit
        $this->assertTrue(true);
    }
}
