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


use Kicaj\Schema\Database\MySQL\Constraint;
use Kicaj\Schema\Itf\TableItf;
use Kicaj\Test\Schema\BaseTest;

/**
 * Constraint_Test.
 *
 * @coversDefaultClass \Kicaj\Schema\Database\MySQL\Constraint
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Constraint_Test extends BaseTest
{
    /**
     * Table mock for tests.
     *
     * @var TableItf
     */
    protected $tableMock;

    protected function setUp()
    {
        $this->tableMock = $this->getMock(TableItf::class);
        $this->tableMock->method('getName')->willReturn('table1');
    }

    public static function setUpBeforeClass()
    {
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', 'table2.sql');
        self::dbLoadFixtures('SCHEMA1', 'table3.sql');
        self::dbLoadFixtures('SCHEMA1', 'table1.sql');
    }

    /**
     * @dataProvider constraintProvider
     *
     * @covers ::__construct
     * @covers ::parseConstraint
     *
     * @covers ::getName
     * @covers ::getForeignKeyName
     * @covers ::getForeignTableName
     * @covers ::getForeignIndexName
     * @covers ::getTable
     *
     * @param string $constraintDef
     * @param array  $expected
     */
    public function test_parseConstraint($constraintDef, $expected)
    {
        // When
        $constraint = new Constraint($constraintDef, $this->tableMock);

        // Then

        $this->assertSame($expected['name'], $constraint->getName());
        $this->assertSame($expected['key_name'], $constraint->getForeignKeyName());
        $this->assertSame($expected['f_table'], $constraint->getForeignTableName());
        $this->assertSame($expected['f_index'], $constraint->getForeignIndexName());

        $this->assertSame('table1', $constraint->getTable()->getName());
    }

    public function constraintProvider()
    {
        return [
            // 0 --------------------------------------------------------------------
            [
                "CONSTRAINT `f1_c` FOREIGN KEY (`f1`) REFERENCES `table2` (`id`) ON UPDATE CASCADE,",
                [
                    'name'     => 'f1_c',
                    'key_name' => 'f1',
                    'f_table'  => 'table2',
                    'f_index'  => 'id',
                ],
            ],
            // 1 --------------------------------------------------------------------
            [
                "CONSTRAINT `rel13` FOREIGN KEY (`f2`) REFERENCES `table3` (`other_id`) ON DELETE NO ACTION",
                [
                    'name'     => 'rel13',
                    'key_name' => 'f2',
                    'f_table'  => 'table3',
                    'f_index'  => 'other_id',
                ],
            ],
            // 2 --------------------------------------------------------------------
            [
                "CONSTRAINT `FKC4E45CCB7609BAD5` FOREIGN KEY (`some_id`) REFERENCES `some_table` (`id`),",
                [
                    'name'     => 'FKC4E45CCB7609BAD5',
                    'key_name' => 'some_id',
                    'f_table'  => 'some_table',
                    'f_index'  => 'id',
                ],
            ],
            // XX --------------------------------------------------------------------
        ];
    }

    /**
     * @covers ::parseConstraint
     *
     * @expectedException \Kicaj\Schema\SchemaException
     * @expectedExceptionMessage Cannot parse index constraint: bad constraint
     */
    public function test_parseConstraint_error()
    {
        new Constraint('bad constraint', $this->tableMock);
    }

    /**
     * @dataProvider isConstraintDefProvider
     *
     * @covers ::isConstraintDef
     *
     * @param string $constraintDef
     * @param bool   $expected
     */
    public function test_isConstraintDef($constraintDef, $expected)
    {
        // When
        $got = Constraint::isConstraintDef($constraintDef);

        // Then
        $this->assertSame($expected, $got);
    }

    public function isConstraintDefProvider()
    {
        return [
            // 0 --------------------------------------------------------------------
            ["CONSTRAINT `f1_c` FOREIGN KEY (`f1`) REFERENCES `table2` (`id`) ON UPDATE CASCADE,", true],
            // 1 --------------------------------------------------------------------
            ["CONSTRAINT `rel13` FOREIGN KEY (`f2`) REFERENCES `table3` (`other_id`) ON DELETE NO ACTION", true],
            // 2 --------------------------------------------------------------------
            ["CONSTRAIN `rel13` FOREIGN KEY (`f2`) REFERENCES `table3` (`other_id`) ON DELETE NO ACTION", false],
        ];
    }

}
