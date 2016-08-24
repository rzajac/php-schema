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
namespace Database\Driver;

use Kicaj\Schema\Database\SchemaFactory;
use Kicaj\Schema\SchemaGetter;
use Kicaj\Test\Helper\Database\DbItf;
use Kicaj\Test\Schema\BaseTest;

/**
 * MYSQL_CreateTable_Test.
 *
 * @coversDefaultClass \Kicaj\Schema\Database\Driver\MySQL
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class MySQL_CreateTable_Test extends BaseTest
{
    /**
     * @var SchemaGetter
     */
    protected $schema;

    /**
     * MySQL database test helper.
     *
     * @var DbItf
     */
    protected $mysql;

    public static function setUpBeforeClass()
    {
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', 'bigtable_create.sql');
    }

    public function setUp()
    {
        parent::setUp();

        $dbConfig = self::getSchemaConfig('SCHEMA1');
        $this->schema = SchemaFactory::factory($dbConfig);
    }

    /**
     * @covers ::dbGetTableDefinition
     */
    public function test_dbTableDefinition()
    {
        // When
        $tableDef = $this->schema->dbGetTableDefinition('bigtable');

        // Then
        $this->assertInstanceOf('\Kicaj\Schema\TableDefinition', $tableDef);
        $this->assertSame('bigtable', $tableDef->getName());
    }

    /**
     * @covers ::dbGetTableDefinition
     */
    public function test_dbGetTableDefinition_columnNames()
    {
        // When
        $tableDef = $this->schema->dbGetTableDefinition('bigtable');
        $columnDefinitions = $tableDef->getColumns();

        // Then
        $this->assertArrayHasKey('id', $columnDefinitions);
        $this->assertArrayHasKey('bt_id', $columnDefinitions);
        $this->assertArrayHasKey('chr', $columnDefinitions);
        $this->assertArrayHasKey('vchr', $columnDefinitions);
        $this->assertArrayHasKey('tint', $columnDefinitions);
        $this->assertArrayHasKey('sint', $columnDefinitions);
        $this->assertArrayHasKey('mint', $columnDefinitions);
        $this->assertArrayHasKey('inte', $columnDefinitions);
        $this->assertArrayHasKey('bint', $columnDefinitions);
        $this->assertArrayHasKey('flt', $columnDefinitions);
        $this->assertArrayHasKey('doub', $columnDefinitions);
        $this->assertArrayHasKey('deci', $columnDefinitions);
        $this->assertArrayHasKey('bt', $columnDefinitions);
        $this->assertArrayHasKey('bol', $columnDefinitions);
        $this->assertArrayHasKey('ttxt', $columnDefinitions);
        $this->assertArrayHasKey('txt', $columnDefinitions);
        $this->assertArrayHasKey('mtxt', $columnDefinitions);
        $this->assertArrayHasKey('ltxt', $columnDefinitions);
        $this->assertArrayHasKey('tblob', $columnDefinitions);
        $this->assertArrayHasKey('mblob', $columnDefinitions);
        $this->assertArrayHasKey('nblob', $columnDefinitions);
        $this->assertArrayHasKey('lblob', $columnDefinitions);
        $this->assertArrayHasKey('bin', $columnDefinitions);
        $this->assertArrayHasKey('vbin', $columnDefinitions);
        $this->assertArrayHasKey('d', $columnDefinitions);
        $this->assertArrayHasKey('dt', $columnDefinitions);
        $this->assertArrayHasKey('ts', $columnDefinitions);
        $this->assertArrayHasKey('tm', $columnDefinitions);
        $this->assertArrayHasKey('y', $columnDefinitions);

        $this->assertSame(29, count($columnDefinitions));
    }

    /**
     * @covers ::dbGetTableDefinition
     */
    public function test_dbGetTableDefinition_indexes()
    {
        // When
        $tableDef = $this->schema->dbGetTableDefinition('bigtable');
        $expected = [
            0 => [
                0 => 'u',
                1 => 'UNIQUE',
                2 => [0 => 'chr'],
            ],
            1 => [
                0 => 'vchr',
                1 => 'UNIQUE',
                2 => [0 => 'vchr'],
            ],
            2 => [
                0 => 'tint',
                1 => 'KEY',
                2 => [0 => 'tint'],
            ],
            3 => [
                0 => 'b',
                1 => 'KEY',
                2 => [0 => 'sint'],
            ],
        ];

        // Then
        $this->assertSame(['', 'PRIMARY', ['id', 'bt_id']], $tableDef->getPrimaryKey());
        $this->assertSame($expected, $tableDef->getIndexes());
    }

    /**
     * @covers ::dbGetTableDefinition
     *
     * @expectedException \Kicaj\DbKit\DatabaseException
     * @expectedExceptionMessage doesn't exist
     */
    public function test_dbGetTableDefinition_error()
    {
        $this->schema->dbGetTableDefinition('not_existing');
    }

    /**
     * @covers ::dbGetTableDefinition
     */
    public function test_dbGetTableDefinition_foreignKeys()
    {
        // Given
        self::dbLoadFixtures('SCHEMA1', ['table2.sql', 'table3.sql', 'table1.sql']);

        // When
        $tableDef = $this->schema->dbGetTableDefinition('table1');

        // Then
        $this->assertTrue($tableDef->hasConstraints());
    }
}
