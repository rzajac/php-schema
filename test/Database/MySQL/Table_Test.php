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

namespace Kicaj\Test\Schema\Database\MySQL;


use Kicaj\Schema\Database\MySQL\Table;
use Kicaj\Schema\Itf\DatabaseItf;
use Kicaj\Schema\Itf\TableItf;
use Kicaj\Test\Schema\BaseTest;


/**
 * Table_Test.
 *
 * @coversDefaultClass \Kicaj\Schema\Database\MySQL\Table
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Table_Test extends BaseTest
{
    /**
     * @covers ::__construct
     * @covers ::parseTableCS
     * @covers ::addColumn
     * @covers ::addIndex
     * @covers ::addConstraint
     * @covers ::getPrimaryKey
     *
     * @covers ::getName
     * @covers ::getType
     * @covers ::getColumns
     * @covers ::getColumnByName
     * @covers ::getIndexes
     * @covers ::getIndexByName
     * @covers ::getConstraints
     * @covers ::getDropStatement
     *
     * @covers \Kicaj\Schema\Database\MySQL\Constraint::getIndex
     */
    public function test_parseTableCS_table()
    {
        // When
        $table = new Table($this->getFixtureRawData('table1.sql'), $this->getMock(DatabaseItf::class));

        // Then
        $this->assertSame('table1', $table->getName());
        $this->assertSame(TableItf::TYPE_TABLE, $table->getType());

        $this->assertSame(['id', 'f1', 'f2'], array_keys($table->getColumns()));
        $this->assertSame('f1', $table->getColumnByName('f1')->getName());

        $this->assertSame(['', 'f1', 'rel13'], array_keys($table->getIndexes()));
        $this->assertSame('f1', $table->getIndexByName('f1')->getName());

        $this->assertSame(['f1_c', 'rel13'], array_keys($table->getConstraints()));

        $this->assertSame('DROP TABLE IF EXISTS `table1`;', $table->getDropStatement());

        $pkColumns = $table->getPrimaryKey()->getColumns();
        $this->assertSame(1, count($pkColumns));
        $this->assertSame('id', $pkColumns['id']->getName());

        $constraints = $table->getConstraints();
        $this->assertSame(2, count($constraints));
        $this->assertArrayHasKey('f1_c', $constraints);
        $this->assertSame('f1', $constraints['f1_c']->getIndex()->getName());
    }

    /**
     * @covers \Kicaj\Schema\Database\MySQL\Column::isPartOfPk
     */
    public function test_column_part_of_pk()
    {
        // Given
        $table = new Table($this->getFixtureRawData('table1.sql'), $this->getMock(DatabaseItf::class));

        // When
        $columnId = $table->getColumnByName('id');
        $columnF1 = $table->getColumnByName('f1');

        // Then
        $this->assertTrue($columnId->isPartOfPk());
        $this->assertFalse($columnF1->isPartOfPk());
    }

    /**
     * @covers \Kicaj\Schema\Database\MySQL\Index::getColumns
     */
    public function test_index_getColumns()
    {
        // Given
        $table = new Table($this->getFixtureRawData('table1.sql'), $this->getMock(DatabaseItf::class));

        // When
        $index = $table->getIndexByName('rel13');
        $columns = $index->getColumns();

        // Then
        $this->assertSame(1, count($columns));
        $this->assertTrue(array_key_exists('f2', $columns));
        $this->assertSame('f2', $columns['f2']->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::parseTableCS
     * @covers ::addColumn
     * @covers ::addIndex
     * @covers ::addConstraint
     *
     * @covers ::getName
     * @covers ::getType
     * @covers ::getColumns
     * @covers ::getColumnByName
     * @covers ::getIndexes
     * @covers ::getIndexByName
     * @covers ::getConstraints
     * @covers ::getDropStatement
     */
    public function test_parseTableCS_view()
    {
        // When
        $table = new Table($this->getFixtureRawData('view.sql'), $this->getMock(DatabaseItf::class));

        // Then
        $this->assertSame('my_view', $table->getName());
        $this->assertSame(TableItf::TYPE_VIEW, $table->getType());

        $this->assertSame([], array_keys($table->getColumns()));
        $this->assertSame([], array_keys($table->getIndexes()));
        $this->assertSame([], array_keys($table->getConstraints()));

        $this->assertSame('DROP VIEW IF EXISTS `my_view`;', $table->getDropStatement());
    }

    /**
     * @covers ::getCreateStatement
     * @covers ::fixCreateStatement
     * @covers ::fixTableCreateStatement
     */
    public function test_getCreateStatement_table()
    {
        // Given
        $table = new Table($this->getFixtureRawData('table1.sql'), $this->getMock(DatabaseItf::class));

        // When
        $fixed = $table->getCreateStatement();
        $fixedIfNotExist = $table->getCreateStatement(true);

        // Then
        $this->assertSame($this->getFixtureRawData('table1_fixed.sql'), $fixed);
        $this->assertSame($this->getFixtureRawData('table1_fixed_if_exists.sql'), $fixedIfNotExist);
    }

    /**
     * @covers ::getCreateStatement
     * @covers ::fixCreateStatement
     * @covers ::fixViewCreateStatement
     */
    public function test_getCreateStatement_view()
    {
        // Given
        $table = new Table($this->getFixtureRawData('view.sql'), $this->getMock(DatabaseItf::class));

        // When
        $fixed = $table->getCreateStatement();
        $fixedIfNotExist = $table->getCreateStatement(true);

        // Then
        $this->assertSame($this->getFixtureRawData('view_fixed.sql'), $fixed);
        $this->assertSame($this->getFixtureRawData('view_fixed_or_replace.sql'), $fixedIfNotExist);
    }

    /**
     * @covers ::getColumnByName
     *
     * @expectedException \Kicaj\Schema\SchemaException
     * @expectedExceptionMessage Table table1 does not have column not_existing.
     */
    public function test_getColumnByName_error()
    {
        // When
        $table = new Table($this->getFixtureRawData('table1.sql'), $this->getMock(DatabaseItf::class));

        // Then
        $table->getColumnByName('not_existing');
    }
}
