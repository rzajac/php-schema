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
namespace Kicaj\Test\Schema\Database\Driver;

use Kicaj\Schema\Database\Driver\MySQL;
use Kicaj\Schema\SchemaGetter;
use Kicaj\Test\Schema\BaseTest;
use Kicaj\DbKit\DatabaseException;
use Kicaj\DbKit\DbConnector;
use Kicaj\Tools\Helper\Str;
use Mockery\Mock;

/**
 * Tests for MySQL driver.
 *
 * @coversDefaultClass \Kicaj\Schema\Database\Driver\MySQL
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
     * @param string $expMsg
     *
     * @internal     param bool $connect
     */
    public function test_connection($host, $username, $password, $database, $port, $expMsg)
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

            $this->assertFalse('' == $expMsg, 'Did not expect to see error: ' . $e->getMessage());
        } finally {
            $this->assertFalse($expMsg !== '' && $thrown === false, 'Expected to see error: ' . $expMsg);
        }
    }

    public function connectionProvider()
    {
        return [
            [
                $GLOBALS['TEST_DB_SCHEMA1_HOST'],
                $GLOBALS['TEST_DB_SCHEMA1_USERNAME'],
                $GLOBALS['TEST_DB_SCHEMA1_PASSWORD'],
                $GLOBALS['TEST_DB_SCHEMA1_DATABASE'],
                3306,
                '',
            ],
            [
                $GLOBALS['TEST_DB_SCHEMA1_HOST'],
                $GLOBALS['TEST_DB_SCHEMA1_USERNAME'],
                $GLOBALS['TEST_DB_SCHEMA1_PASSWORD'],
                'not_existing',
                3306,
                "'not_existing'",
            ],
        ];
    }

    /**
     * @covers ::dbGetTableNames
     */
    public function test_getDbTableNames()
    {
        // When
        $tableNames = $this->driver->dbGetTableNames();

        // Then
        $this->assertSame(['bigtable'], $tableNames);
    }

    /**
     * @covers ::dbGetCreateStatement
     */
    public function test_getDbCreateStatement()
    {
        // Given
        self::dbDropTables('SCHEMA1', 'bigtable');
        self::dbLoadFixtures('SCHEMA1', 'bigtable_create.sql');

        // When
        $expCreate = self::getFixtureData('bigtable_create.sql');
        $gotCreate = $this->driver->dbGetCreateStatement('bigtable');

        // Then
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
     *
     * @expectedException \Kicaj\DbKit\DatabaseException
     * @expectedExceptionMessage doesn't exist
     */
    public function test_getDbCreateStatement_err()
    {
        // When
        $gotCreate = $this->driver->dbGetCreateStatement('notExisting');

        // Then
        $this->assertSame([], $gotCreate);
    }

    /**
     * @covers ::dbGetCreateStatement
     * @covers ::fixCreateStatement
     */
    public function test_getDbCreateStatement_ifNotExist()
    {
        // Given
        self::dbDropTables('SCHEMA1', 'bigtable');
        self::dbLoadFixtures('SCHEMA1', 'bigtable_create.sql');

        // When
        $gotCreate = $this->driver->dbGetCreateStatement('bigtable', true);
        $gotCreate['create'] = Str::oneLine($gotCreate['create']);

        // Then
        $this->assertContains('IF NOT EXISTS ', $gotCreate['create']);
    }

    /**
     * @covers ::dbGetCreateStatements
     */
    public function test_getDbCreateStatements()
    {
        // Given
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', ['bigtable_create.sql', 'test1.sql']);

        // When
        $gotCreates = $this->driver->dbGetCreateStatements();

        // Then
        $this->assertSame(2, count(array_keys($gotCreates)));
        $this->assertArrayHasKey('bigtable', $gotCreates);
        $this->assertArrayHasKey('test1', $gotCreates);
    }

    /**
     * @covers ::dbGetCreateStatements
     * @covers ::fixCreateStatement
     * @covers ::getRowsArray
     */
    public function test_getDbCreateStatements_indexes()
    {
        // Given
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', ['bigtable_create.sql', 'test1.sql', 'view.sql']);

        // When
        $tableDef = $this->driver->dbGetTableDefinition('bigtable');

        // Then
        $this->assertSame(SchemaGetter::CREATE_TYPE_TABLE, $tableDef->getType());
        $this->assertSame(4, count($tableDef->getIndexes()));
        $this->assertNotEmpty($tableDef->getPrimaryKey());
    }

    /**
     * @covers ::dbGetCreateStatement
     * @covers ::fixCreateStatement
     * @covers ::getRowsArray
     */
    public function test_getDbCreateStatements_view()
    {
        // Given
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', ['test1.sql', 'view.sql']);

        // When
        $arr = $this->driver->dbGetCreateStatement('myview', true);

        // Then
        $this->assertSame(SchemaGetter::CREATE_TYPE_VIEW, $arr['type']);
        $this->assertSame('myview', $arr['name']);
        $this->assertSame('DROP VIEW IF EXISTS `myview`;', $arr['drop']);
    }

    /**
     * @covers ::dbGetCreateStatement
     */
    public function test_getDbCreateStatements_empty_getRowsArray()
    {
        // Given
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', ['test1.sql', 'view.sql']);

        /** @var MySQL|Mock $mock */
        $mock = \Mockery::mock('\Kicaj\Schema\Database\Driver\MySQL')->makePartial();
        $mock->shouldReceive('getRowsArray')->times(1)->andReturn([[]]);
        $mock->dbSetup(self::dbGetConfig('SCHEMA1'))->dbConnect();

        // When
        $arr = $mock->dbGetCreateStatement('myview', true);

        // Then
        $this->assertSame('', $arr['create']);
        $this->assertSame('', $arr['drop']);
        $this->assertSame(SchemaGetter::CREATE_TYPE_NONE, $arr['type']);
        $this->assertSame('', $arr['name']);
    }

    /**
     * @covers ::dbGetCreateStatement
     */
    public function test_getDbCreateStatements_unknownType()
    {
        // Given
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', ['test1.sql', 'view.sql']);

        // When
        /** @var MySQL|Mock $mock */
        $mock = \Mockery::mock('\Kicaj\Schema\Database\Driver\MySQL')->makePartial();
        $mock->shouldReceive('getRowsArray')->times(1)->andReturn([['Unknown Create' => '']]);
        $mock->dbSetup(self::dbGetConfig('SCHEMA1'))->dbConnect();

        $arr = $mock->dbGetCreateStatement('myview', true);

        // Then
        $this->assertSame('', $arr['create']);
        $this->assertSame('', $arr['drop']);
        $this->assertSame(SchemaGetter::CREATE_TYPE_NONE, $arr['type']);
        $this->assertSame('', $arr['name']);
    }

    /**
     * @dataProvider parseIndexProvider
     *
     * @covers ::parseIndex
     *
     * @param string   $keyDef
     * @param string   $name
     * @param string   $type
     * @param string[] $colNames
     *
     * @throws \Kicaj\Schema\SchemaException
     */
    public function test_parseIndex($keyDef, $name, $type, $colNames)
    {
        // When
        $got = MySQL::parseIndex($keyDef);

        // Then
        $this->assertSame($name, $got[0]);
        $this->assertSame($type, $got[1]);
        $this->assertSame($colNames, $got[2]);
    }

    public function parseIndexProvider()
    {
        return [
            ['PRIMARY KEY (`id`)', '', 'PRIMARY', ['id']],
        ];
    }

    /**
     * @covers ::parseIndex
     *
     * @expectedException \Kicaj\Schema\SchemaException
     * @expectedExceptionMessage cannot parse table index: wrong format
     */
    public function test_parseIndex_error()
    {
        MySQL::parseIndex('wrong format');
    }

    /**
     * @covers ::dbGetTableDropCommand
     */
    public function test_getDbTableDropCommand()
    {
        // When
        $exp = 'DROP TABLE IF EXISTS `tableX`;';
        $got = $this->driver->dbGetTableDropCommand('tableX', SchemaGetter::CREATE_TYPE_TABLE);

        // Then
        $this->assertSame($exp, $got);
    }

    /**
     * @covers ::dbGetTableDropCommand
     */
    public function test_getDbTableDropCommand_view()
    {
        // When
        $exp = 'DROP VIEW IF EXISTS `viewX`;';
        $got = $this->driver->dbGetTableDropCommand('viewX', SchemaGetter::CREATE_TYPE_VIEW);

        // Then
        $this->assertSame($exp, $got);
    }

    /**
     * @covers ::dbGetTableDropCommand
     *
     * @expectedException \Kicaj\Schema\SchemaException
     * @expectedExceptionMessage Unknown table type: not_existing_type
     */
    public function test_getDbTableDropCommand_error()
    {
        $this->driver->dbGetTableDropCommand('tableX', 'not_existing_type');
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

    /**
     * @covers ::runQuery
     *
     * @expectedException \Kicaj\DbKit\DatabaseException
     */
    public function test_runQuery_error()
    {
        $this->driver->runQuery('NOT SQL');
    }

    /**
     * @covers ::runQuery
     */
    public function test_test_runQuery()
    {
        // When
        $result = $this->driver->runQuery('SELECT NOW();');

        // Then
        $this->assertInstanceOf('\mysqli_result', $result);
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
}
