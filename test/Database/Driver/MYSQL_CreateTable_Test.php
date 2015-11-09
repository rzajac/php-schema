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


use Kicaj\SchemaDump\Database\SchemaDumpFactory;
use Kicaj\SchemaDump\SchemaGetter;
use Kicaj\Test\SchemaDump\BaseTest;

/**
 * MYSQL_CreateTable_Test.
 *
 * @coversDefaultClass \Kicaj\SchemaDump\Database\Driver\MySQL
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class MYSQL_CreateTable_Test extends BaseTest
{
    protected static $residentFixtures = ['bigtable_create.sql'];

    /**
     * @var SchemaGetter
     */
    protected $schema;

    public function setUp()
    {
        parent::setUp();

        $this->schema = SchemaDumpFactory::factory(self::dbGetConfig());
    }


    /**
     * @covers ::dbGetTableDefinition
     */
    public function test_dbTableDefinition()
    {
        $tableDef = $this->schema->dbGetTableDefinition('bigtable');

        $this->assertInstanceOf('\Kicaj\SchemaDump\TableDefinition', $tableDef);
        $this->assertSame('bigtable', $tableDef->getName());
    }

    /**
     * @covers ::dbGetTableDefinition
     */
    public function test_dbGetTableDefinition_columnNames()
    {
        $tableDef = $this->schema->dbGetTableDefinition('bigtable');
        $columnDefinitions = $tableDef->getColumns();

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
        $tableDef = $this->schema->dbGetTableDefinition('bigtable');

        $this->assertSame(['', 'PRIMARY', ['id', 'bt_id']], $tableDef->getPrimaryKey());

        $expected = [
            0 => [
                    0 => 'u',
                    1 => 'UNIQUE',
                    2 => [0 => 'chr',],
            ],
            1 => [
                    0 => 'vchr',
                    1 => 'UNIQUE',
                    2 => [0 => 'vchr',],
                ],
            2 => [
                    0 => 'tint',
                    1 => 'KEY',
                    2 => [0 => 'tint',],
            ],
            3 => [
                    0 => 'b',
                    1 => 'KEY',
                    2 => [0 => 'sint',],
            ],
        ];

        $this->assertSame($expected, $tableDef->getIndexes());
    }
}
