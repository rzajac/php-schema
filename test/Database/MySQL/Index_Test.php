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

namespace Kicaj\Test\Schema\Database\MySQL;

use Kicaj\Schema\Database\MySQL\Index;
use Kicaj\Schema\Itf\IndexItf;
use Kicaj\Schema\Itf\TableItf;
use Kicaj\Test\Schema\BaseTest;

/**
 * Index_Test.
 *
 * @coversDefaultClass \Kicaj\Schema\Database\MySQL\Index
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Index_Test extends BaseTest
{
    /**
     * Table mock for tests.
     *
     * @var TableItf
     */
    protected $tableMock;

    protected function setUp()
    {
        $this->tableMock = $this->getMockBuilder(TableItf::class)->getMock();
        $this->tableMock->method('getName')->willReturn('table1');
    }

    /**
     * @dataProvider indexProvider
     *
     * @covers ::__construct
     * @covers ::getName
     * @covers ::getType
     * @covers ::getColumnNames
     * @covers ::getTable
     *
     * @covers ::parseIndexDef
     *
     * @param string $indexDef
     * @param array  $expected
     *
     * @throws \Kicaj\Schema\SchemaEx
     */
    public function test_parseIndex($indexDef, $expected)
    {
        // When
        $index = new Index($indexDef, $this->tableMock);

        // Then
        $this->assertSame($expected['name'], $index->getName());
        $this->assertSame($expected['type'], $index->getType());
        $this->assertSame($expected['column_names'], $index->getColumnNames());
        $this->assertSame('table1', $index->getTable()->getName());
    }

    public function indexProvider()
    {
        return [
            // 0 --------------------------------------------------------------------
            [
                "PRIMARY KEY (`id`,`bt_id`),",
                [
                    'name'         => '',
                    'type'         => IndexItf::PRIMARY,
                    'column_names' => ['id', 'bt_id'],
                ],
            ],
            // 1 --------------------------------------------------------------------
            [
                "UNIQUE KEY `u` (`chr`),",
                [
                    'name'         => 'u',
                    'type'         => IndexItf::UNIQUE,
                    'column_names' => ['chr'],
                ],
            ],
            // 2 --------------------------------------------------------------------
            [
                "UNIQUE KEY `abc` (`some_col1`, `some_col2`),",
                [
                    'name'         => 'abc',
                    'type'         => IndexItf::UNIQUE,
                    'column_names' => ['some_col1', 'some_col2'],
                ],
            ],
            // 3 --------------------------------------------------------------------
            [
                "KEY `tint` (`tint1`),",
                [
                    'name'         => 'tint',
                    'type'         => IndexItf::KEY,
                    'column_names' => ['tint1'],
                ],
            ],
            // 4 --------------------------------------------------------------------
            [
                "KEY `tint` (`tint1`, `tint2`),",
                [
                    'name'         => 'tint',
                    'type'         => IndexItf::KEY,
                    'column_names' => ['tint1', 'tint2'],
                ],
            ],
            // x --------------------------------------------------------------------
        ];
    }

    /**
     * @dataProvider isIndexDefProvider
     *
     * @covers ::isIndexDef
     *
     * @param $indexDef
     * @param $expected
     */
    public function test_isIndexDef($indexDef, $expected)
    {
        // When
        $got = Index::isIndexDef($indexDef);

        // Then
        $this->assertSame($expected, $got);

    }

    public function isIndexDefProvider()
    {
        return [
            ["PRIMARY KEY (`id`,`bt_id`),", true],
            ["UNIQUE KEY `u` (`chr`),", true],
            ["UNIQUE KEY `abc` (`some_col1`, `some_col2`),", true],
            ["KEY `tint` (`tint1`),", true],
            ["KEY `tint` (`tint1`, `tint2`),", true],
            ["MYKEY (`id`,`bt_id`),", false],
        ];
    }

    /**
     * @covers ::parseIndexDef
     *
     * @expectedException \Kicaj\Schema\SchemaEx
     * @expectedExceptionMessage Cannot parse index definition: bad index definition
     */
    public function test_parseIndexDef_error()
    {
        new Index('bad index definition', $this->tableMock);
    }

    /**
     * @covers ::getColumnNames
     *
     * @throws \Kicaj\Schema\SchemaEx
     */
    public function test_getColumnNames()
    {
        // Given
        $index = new Index("PRIMARY KEY (`id`)", $this->tableMock);

        // When
        $columns = $index->getColumnNames();

        // Then
        $this->assertSame(['id'], $columns);
    }

    /**
     * @covers ::getColumnNames
     *
     * @throws \Kicaj\Schema\SchemaEx
     */
    public function test_getColumnNames_multiple()
    {
        // Given
        $index = new Index("KEY `my_key` (`id`, `name`)", $this->tableMock);

        // When
        $columns = $index->getColumnNames();

        // Then
        $this->assertSame(['id', 'name'], $columns);
    }
}
