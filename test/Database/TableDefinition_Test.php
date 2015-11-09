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

namespace Database;

use Kicaj\SchemaDump\ColumnDefinition;
use Kicaj\SchemaDump\TableDefinition;
use Kicaj\Tools\Db\DbConnector;

/**
 * TableDefinition_Test.
 * 
 * @coversDefaultClass \Kicaj\SchemaDump\TableDefinition
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class TableDefinition_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::make
     * @covers ::getName
     */
    public function test___construct()
    {
        $td = TableDefinition::make('testTable');

        $this->assertInstanceOf('\Kicaj\SchemaDump\TableDefinition', $td);
        $this->assertSame('testTable', $td->getName());
    }

    /**
     * @covers ::addColumn
     * @covers ::getColumns
     */
    public function test_addColumn()
    {
        $td = TableDefinition::make('testTable');

        $col1 = new ColumnDefinition('col1', DbConnector::DB_DRIVER_MYSQL, 'testTable');
        $col2 = new ColumnDefinition('col2', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $td->addColumn($col1);
        $td->addColumn($col2);

        $gotColumns = $td->getColumns();
        $this->assertSame(2, count($gotColumns));
        $this->assertSame($col1, $gotColumns['col1']);
        $this->assertSame($col2, $gotColumns['col2']);
    }

    /**
     * @covers ::addIndex
     * @covers ::getIndexes
     * @covers ::getPrimaryKey
     */
    public function test_addIndex()
    {
        $index1 = ['indexName1', 'PRIMARY', ['col1', 'col2'] ];
        $index2 = ['indexName2', 'UNIQUE', ['col3'] ];
        $index3 = ['indexName3', 'KEY', ['col4'] ];

        $td = TableDefinition::make('testTable');
        $td->addIndex($index1);
        $td->addIndex($index2);
        $td->addIndex($index3);

        $gotIndexes = $td->getIndexes();
        $this->assertSame(2, count($gotIndexes));
        $this->assertSame($index2, $gotIndexes[0]);
        $this->assertSame($index3, $gotIndexes[1]);
        $this->assertSame($index1, $td->getPrimaryKey());
    }
}
