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
namespace Kicaj\Test\Schema;

use Kicaj\Schema\ColumnDefinition;
use Kicaj\Schema\SchemaGetter;
use Kicaj\Schema\TableDefinition;
use Kicaj\DbKit\DbConnector;

/**
 * TableDefinition_Test.
 * 
 * @coversDefaultClass \Kicaj\Schema\TableDefinition
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class TableDefinition_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::make
     * @covers ::getName
     * @covers ::getType
     */
    public function test___construct_table()
    {
        // When
        $td = TableDefinition::make('testTable');

        // Then
        $this->assertInstanceOf('\Kicaj\Schema\TableDefinition', $td);
        $this->assertSame('testTable', $td->getName());
        $this->assertSame(SchemaGetter::CREATE_TYPE_TABLE, $td->getType());

        $td = TableDefinition::make('testView', SchemaGetter::CREATE_TYPE_VIEW);

        $this->assertInstanceOf('\Kicaj\Schema\TableDefinition', $td);
        $this->assertSame('testView', $td->getName());
        $this->assertSame(SchemaGetter::CREATE_TYPE_VIEW, $td->getType());
    }

    /**
     * @covers ::__construct
     * @covers ::make
     * @covers ::getName
     * @covers ::getType
     */
    public function test___construct_view()
    {
        // When
        $td = TableDefinition::make('testView', SchemaGetter::CREATE_TYPE_VIEW);

        // Then
        $this->assertInstanceOf('\Kicaj\Schema\TableDefinition', $td);
        $this->assertSame('testView', $td->getName());
        $this->assertSame(SchemaGetter::CREATE_TYPE_VIEW, $td->getType());
    }

    /**
     * @covers ::addColumn
     * @covers ::getColumns
     */
    public function test_addColumn()
    {
        // Given
        $td = TableDefinition::make('testTable');

        // When
        $col1 = new ColumnDefinition('col1', DbConnector::DB_DRIVER_MYSQL, 'testTable');
        $col2 = new ColumnDefinition('col2', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $td->addColumn($col1);
        $td->addColumn($col2);

        // Then
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
        // Given - Indexes definition
        $index1 = ['indexName1', 'PRIMARY', ['col1', 'col2']];
        $index2 = ['indexName2', 'UNIQUE', ['col3']];
        $index3 = ['indexName3', 'KEY', ['col4']];

        // When - Columns for primary key
        $col1 = new ColumnDefinition('col1', DbConnector::DB_DRIVER_MYSQL, 'testTable');
        $col2 = new ColumnDefinition('col2', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // Construct test table definition
        $td = TableDefinition::make('testTable');
        $td->addColumn($col1);
        $td->addColumn($col2);
        $td->addIndex($index1);
        $td->addIndex($index2);
        $td->addIndex($index3);

        // Then
        $gotIndexes = $td->getIndexes();
        $this->assertSame(2, count($gotIndexes));
        $this->assertSame($index2, $gotIndexes[0]);
        $this->assertSame($index3, $gotIndexes[1]);
        $this->assertSame($index1, $td->getPrimaryKey());
    }

    /**
     * @covers ::addConstraint
     * @covers ::hasConstraints
     * @covers ::getConstraints
     */
    public function test_constraints()
    {
        // Given - Indexes definition
        $c1 = ['rel12', 'f1', 't2', 'id2'];
        $c2 = ['rel13', 'f3', 't3', 'id3'];

        // Construct test table definition
        $td = TableDefinition::make('testTable');
        $td->addConstraint($c1);
        $td->addConstraint($c2);
        $gotConstraints = $td->getConstraints();

        // Then
        $this->assertTrue($td->hasConstraints());
        $this->assertSame(2, count($gotConstraints));
        $this->assertSame($c1, $gotConstraints[0]);
        $this->assertSame($c2, $gotConstraints[1]);
    }
}
