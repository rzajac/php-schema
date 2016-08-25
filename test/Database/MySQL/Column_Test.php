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

use Kicaj\DbKit\DbConnector;
use Kicaj\Schema\Database\MySQL\Column;
use Kicaj\Schema\Database\MySQL\MySQL;
use Kicaj\Schema\Itf\ColumnItf;
use Kicaj\Schema\Itf\TableItf;
use Kicaj\Test\Schema\BaseTest;

/**
 * Column_Test.
 *
 * @coversDefaultClass \Kicaj\Schema\Database\MySQL\Column
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Column_Test extends BaseTest
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
        $this->tableMock->method('getName')->willReturn('table2');
    }

    /**
     * @dataProvider columnParserProvider
     *
     * @covers ::__construct
     * @covers ::parseColumn
     * @covers ::parseMySQLType
     * @covers ::parseUnsigned
     * @covers ::parseAndSetColExtra
     * @covers ::isDbNumberType
     * @covers ::setLengthsAndValidValues
     * @covers ::setTypeBounds
     * @covers ::setDefaultValue
     * @covers ::isPartOfPk
     *
     * @covers ::getName
     * @covers ::getDbType
     * @covers ::getPhpType
     * @covers ::isNullAllowed
     * @covers ::isAutoincrement
     * @covers ::getDefaultValue
     * @covers ::isUnsigned
     * @covers ::getMinLength
     * @covers ::getMaxLength
     * @covers ::getMinValue
     * @covers ::getMaxValue
     * @covers ::getValidValues
     * @covers ::getPosition
     *
     * @covers ::getTable
     * @covers ::getDriverName
     *
     * @param string $columnDef
     * @param array  $expected
     */
    public function test_columnParser($columnDef, array $expected)
    {
        // Given
        $index = rand(0, 200);

        // When
        $column = new Column($columnDef, $index, $this->tableMock);

        // Then
        $this->assertSame($expected['name'], $column->getName());
        $this->assertSame($expected['db_type'], $column->getDbType());
        $this->assertSame($expected['php_type'], $column->getPhpType());
        $this->assertSame($expected['allow_null'], $column->isNullAllowed());
        $this->assertSame($expected['auto_increment'], $column->isAutoincrement());
        $this->assertSame($expected['default_val'], $column->getDefaultValue());

        $this->assertSame($expected['unsigned'], $column->isUnsigned());
        $this->assertSame($expected['min_length'], $column->getMinLength());
        $this->assertSame($expected['max_length'], $column->getMaxLength());
        $this->assertSame($expected['min_value'], $column->getMinValue());
        $this->assertSame($expected['max_value'], $column->getMaxValue());

        $this->assertSame($expected['valid_values'], $column->getValidValues());

        $this->assertFalse($column->isPartOfPk());
        $this->assertSame($index, $column->getPosition());
        $this->assertSame('table2', $column->getTable()->getName());
        $this->assertSame(DbConnector::DB_DRIVER_MYSQL, $column->getDriverName());
    }

    public function columnParserProvider()
    {
        return [
            // 0 --------------------------------------------------------------------
            [
                "  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,",
                [
                    'name'           => 'id',
                    'db_type'        => MySQL::TYPE_INT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => false,
                    'auto_increment' => true,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 4294967295,
                    'valid_values'   => null,
                ],
            ],
            // 1 --------------------------------------------------------------------
            [
                " `id` int(11) NOT NULL AUTO_INCREMENT,",
                [
                    'name'           => 'id',
                    'db_type'        => MySQL::TYPE_INT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => false,
                    'auto_increment' => true,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => -2147483648,
                    'max_value'      => 2147483647,
                    'valid_values'   => null,
                ],
            ],
            // 2 --------------------------------------------------------------------
            [
                "  `t1_id` int(10) unsigned NOT NULL,",
                [
                    'name'           => 't1_id',
                    'db_type'        => MySQL::TYPE_INT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => false,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 4294967295,
                    'valid_values'   => null,
                ],
            ],
            // 3 --------------------------------------------------------------------
            [
                "`col1` int(11) unsigned NOT NULL DEFAULT '123',",
                [
                    'name'           => 'col1',
                    'db_type'        => MySQL::TYPE_INT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => false,
                    'auto_increment' => false,
                    'default_val'    => 123,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 4294967295,
                    'valid_values'   => null,
                ],
            ],
            // 4 --------------------------------------------------------------------
            [
                " `col1` int(11) DEFAULT NULL",
                [
                    'name'           => 'col1',
                    'db_type'        => MySQL::TYPE_INT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => -2147483648,
                    'max_value'      => 2147483647,
                    'valid_values'   => null,
                ],
            ],
            // 5 --------------------------------------------------------------------
            [
                " `col1` int(11) DEFAULT '123'",
                [
                    'name'           => 'col1',
                    'db_type'        => MySQL::TYPE_INT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => 123,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => -2147483648,
                    'max_value'      => 2147483647,
                    'valid_values'   => null,
                ],
            ],
            // 6 --------------------------------------------------------------------
            [
                " `col1` int(11) unsigned DEFAULT NULL,",
                [
                    'name'           => 'col1',
                    'db_type'        => MySQL::TYPE_INT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 4294967295,
                    'valid_values'   => null,
                ],
            ],
            // 7 --------------------------------------------------------------------
            [
                "   `tint` tinyint(4) DEFAULT NULL,",
                [
                    'name'           => 'tint',
                    'db_type'        => MySQL::TYPE_TINYINT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => -128,
                    'max_value'      => 127,
                    'valid_values'   => null,
                ],
            ],
            // 8 --------------------------------------------------------------------
            [
                "   `tint` tinyint(4) unsigned DEFAULT NULL,",
                [
                    'name'           => 'tint',
                    'db_type'        => MySQL::TYPE_TINYINT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 255,
                    'valid_values'   => null,
                ],
            ],
            // 9 --------------------------------------------------------------------
            [
                "  `chr` char(1) DEFAULT NULL,",
                [
                    'name'           => 'chr',
                    'db_type'        => MySQL::TYPE_CHAR,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 1,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 10 --------------------------------------------------------------------
            [
                "  `vchr` varchar(2) DEFAULT NULL,",
                [
                    'name'           => 'vchr',
                    'db_type'        => MySQL::TYPE_VARCHAR,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 2,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 11 --------------------------------------------------------------------
            [
                "  `sint` smallint(6) DEFAULT NULL,",
                [
                    'name'           => 'sint',
                    'db_type'        => MySQL::TYPE_SMALLINT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => -32768,
                    'max_value'      => 32767,
                    'valid_values'   => null,
                ],
            ],
            // 12 --------------------------------------------------------------------
            [
                "  `sint` smallint(6) unsigned DEFAULT NULL,",
                [
                    'name'           => 'sint',
                    'db_type'        => MySQL::TYPE_SMALLINT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 65535,
                    'valid_values'   => null,
                ],
            ],
            // 13 --------------------------------------------------------------------
            [
                "  `mint` mediumint(9) DEFAULT NULL,",
                [
                    'name'           => 'mint',
                    'db_type'        => MySQL::TYPE_MEDIUMINT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => -8388608,
                    'max_value'      => 8388607,
                    'valid_values'   => null,
                ],
            ],
            // 14 --------------------------------------------------------------------
            [
                "  `mint` mediumint(9) unsigned DEFAULT NULL,",
                [
                    'name'           => 'mint',
                    'db_type'        => MySQL::TYPE_MEDIUMINT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 16777215,
                    'valid_values'   => null,
                ],
            ],
            // 15 --------------------------------------------------------------------
            [
                "  `inte` int(11) DEFAULT NULL,",
                [
                    'name'           => 'inte',
                    'db_type'        => MySQL::TYPE_INT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => -2147483648,
                    'max_value'      => 2147483647,
                    'valid_values'   => null,
                ],
            ],
            // 16 --------------------------------------------------------------------
            [
                "  `inte` int(11) unsigned DEFAULT NULL,",
                [
                    'name'           => 'inte',
                    'db_type'        => MySQL::TYPE_INT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 4294967295,
                    'valid_values'   => null,
                ],
            ],
            // 17 --------------------------------------------------------------------
            [
                "  `bint` bigint(20) DEFAULT NULL,",
                [
                    'name'           => 'bint',
                    'db_type'        => MySQL::TYPE_BIGINT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => -9223372036854775808,
                    'max_value'      => 9223372036854775807,
                    'valid_values'   => null,
                ],
            ],
            // 18 --------------------------------------------------------------------
            [
                "  `bint` bigint(20) unsigned DEFAULT NULL,",
                [
                    'name'           => 'bint',
                    'db_type'        => MySQL::TYPE_BIGINT,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 18446744073709551615,
                    'valid_values'   => null,
                ],
            ],
            // 19 --------------------------------------------------------------------
            [
                "  `flt` float DEFAULT NULL,",
                [
                    'name'           => 'flt',
                    'db_type'        => MySQL::TYPE_FLOAT,
                    'php_type'       => ColumnItf::PHP_TYPE_FLOAT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 20 --------------------------------------------------------------------
            [
                "  `flt` float unsigned DEFAULT NULL,",
                [
                    'name'           => 'flt',
                    'db_type'        => MySQL::TYPE_FLOAT,
                    'php_type'       => ColumnItf::PHP_TYPE_FLOAT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 21 --------------------------------------------------------------------
            [
                "  `doub` double DEFAULT NULL,",
                [
                    'name'           => 'doub',
                    'db_type'        => MySQL::TYPE_DOUBLE,
                    'php_type'       => ColumnItf::PHP_TYPE_FLOAT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 22 --------------------------------------------------------------------
            [
                "  `doub` double unsigned DEFAULT NULL,",
                [
                    'name'           => 'doub',
                    'db_type'        => MySQL::TYPE_DOUBLE,
                    'php_type'       => ColumnItf::PHP_TYPE_FLOAT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 23 --------------------------------------------------------------------
            [
                " `deci` decimal(10,0) DEFAULT NULL,",
                [
                    'name'           => 'deci',
                    'db_type'        => MySQL::TYPE_DECIMAL,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 24 --------------------------------------------------------------------
            [
                " `deci` decimal(10,0) unsigned DEFAULT NULL,",
                [
                    'name'           => 'deci',
                    'db_type'        => MySQL::TYPE_DECIMAL,
                    'php_type'       => ColumnItf::PHP_TYPE_INT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 25 --------------------------------------------------------------------
            [
                "   `bt` bit(1) DEFAULT NULL,",
                [
                    'name'           => 'bt',
                    'db_type'        => MySQL::TYPE_BIT,
                    'php_type'       => ColumnItf::PHP_TYPE_BOOL,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => null,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 1,
                    'valid_values'   => null,
                ],
            ],
            // 26 --------------------------------------------------------------------
            [
                "`ttxt` tinytext,",
                [
                    'name'           => 'ttxt',
                    'db_type'        => MySQL::TYPE_TINYTEXT,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 255,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 27 --------------------------------------------------------------------
            [
                "  `txt` text,",
                [
                    'name'           => 'txt',
                    'db_type'        => MySQL::TYPE_TEXT,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 65535,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 28 --------------------------------------------------------------------
            [
                " `mtxt` mediumtext,",
                [
                    'name'           => 'mtxt',
                    'db_type'        => MySQL::TYPE_MEDIUMTEXT,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 16777215,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 29 --------------------------------------------------------------------
            [
                " `ltxt` longtext,",
                [
                    'name'           => 'ltxt',
                    'db_type'        => MySQL::TYPE_LONGTEXT,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 4294967295,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 30 --------------------------------------------------------------------
            [
                "  `tblob` tinyblob,",
                [
                    'name'           => 'tblob',
                    'db_type'        => MySQL::TYPE_TINYBLOB,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 255,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 31 --------------------------------------------------------------------
            [
                " `nblob` blob,",
                [
                    'name'           => 'nblob',
                    'db_type'        => MySQL::TYPE_BLOB,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 65535,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 32 --------------------------------------------------------------------
            [
                " `lblob` longblob,",
                [
                    'name'           => 'lblob',
                    'db_type'        => MySQL::TYPE_LONGBLOB,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 4294967295,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 33 --------------------------------------------------------------------
            [
                " `bin` binary(1) DEFAULT NULL,",
                [
                    'name'           => 'bin',
                    'db_type'        => MySQL::TYPE_BINARY,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 1,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 34 --------------------------------------------------------------------
            [
                " `bin` binary(5) DEFAULT NULL,",
                [
                    'name'           => 'bin',
                    'db_type'        => MySQL::TYPE_BINARY,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 5,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 35 --------------------------------------------------------------------
            [
                " `vbin` varbinary(20) DEFAULT NULL,",
                [
                    'name'           => 'vbin',
                    'db_type'        => MySQL::TYPE_VARBINARY,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 20,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 36 --------------------------------------------------------------------
            [
                " `d` date DEFAULT '2015-10-20',",
                [
                    'name'           => 'd',
                    'db_type'        => MySQL::TYPE_DATE,
                    'php_type'       => ColumnItf::PHP_TYPE_DATE,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => '2015-10-20',
                    'unsigned'       => null,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => '1000-01-01',
                    'max_value'      => '9999-12-31',
                    'valid_values'   => null,
                ],
            ],
            // 37 --------------------------------------------------------------------
            [
                " `dt` datetime DEFAULT NULL,",
                [
                    'name'           => 'dt',
                    'db_type'        => MySQL::TYPE_DATETIME,
                    'php_type'       => ColumnItf::PHP_TYPE_DATETIME,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => '1000-01-01 00:00:00',
                    'max_value'      => '9999-12-31 23:59:59',
                    'valid_values'   => null,
                ],
            ],
            // 38 --------------------------------------------------------------------
            [
                "  `ts` timestamp NULL DEFAULT NULL,",
                [
                    'name'           => 'ts',
                    'db_type'        => MySQL::TYPE_TIMESTAMP,
                    'php_type'       => ColumnItf::PHP_TYPE_TIMESTAMP,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 2147483647,
                    'valid_values'   => null,
                ],
            ],
            // 39 --------------------------------------------------------------------
            [
                "  `tm` time DEFAULT NULL,",
                [
                    'name'           => 'tm',
                    'db_type'        => MySQL::TYPE_TIME,
                    'php_type'       => ColumnItf::PHP_TYPE_TIME,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => '00:00:00',
                    'max_value'      => '23:59:59',
                    'valid_values'   => null,
                ],
            ],
            // 40 --------------------------------------------------------------------
            [
                " `y` year(4) DEFAULT '2015',",
                [
                    'name'           => 'y',
                    'db_type'        => MySQL::TYPE_YEAR,
                    'php_type'       => ColumnItf::PHP_TYPE_YEAR,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => 2015,
                    'unsigned'       => true,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 1901,
                    'max_value'      => 2155,
                    'valid_values'   => null,
                ],
            ],
            // 41 --------------------------------------------------------------------
            [
                " `e` enum('0','XS','s p a c e') DEFAULT 'XS',",
                [
                    'name'           => 'e',
                    'db_type'        => MySQL::TYPE_ENUM,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => 'XS',
                    'unsigned'       => null,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => ['0', 'XS', 's p a c e'],
                ],
            ],
            // 42 --------------------------------------------------------------------
            [
                "  `s` set('0','XS','s p a c e') DEFAULT '0',",
                [
                    'name'           => 's',
                    'db_type'        => MySQL::TYPE_SET,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => '0',
                    'unsigned'       => null,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => ['0', 'XS', 's p a c e'],
                ],
            ],
            // 43 --------------------------------------------------------------------
            [
                "  `flt` float DEFAULT '0.1',",
                [
                    'name'           => 'flt',
                    'db_type'        => MySQL::TYPE_FLOAT,
                    'php_type'       => ColumnItf::PHP_TYPE_FLOAT,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => 0.1,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // 44 --------------------------------------------------------------------
            [
                "   `bt` bit(1) DEFAULT '1',",
                [
                    'name'           => 'bt',
                    'db_type'        => MySQL::TYPE_BIT,
                    'php_type'       => ColumnItf::PHP_TYPE_BOOL,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => true,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 1,
                    'valid_values'   => null,
                ],
            ],
            // 45 --------------------------------------------------------------------
            [
                "   `bt` bit(1) DEFAULT '0',",
                [
                    'name'           => 'bt',
                    'db_type'        => MySQL::TYPE_BIT,
                    'php_type'       => ColumnItf::PHP_TYPE_BOOL,
                    'allow_null'     => true,
                    'auto_increment' => false,
                    'default_val'    => false,
                    'unsigned'       => false,
                    'min_length'     => null,
                    'max_length'     => null,
                    'min_value'      => 0,
                    'max_value'      => 1,
                    'valid_values'   => null,
                ],
            ],
            // 46 --------------------------------------------------------------------
            [
                " `mblob` mediumblob,",
                [
                    'name'           => 'mblob',
                    'db_type'        => MySQL::TYPE_MEDIUMBLOB,
                    'php_type'       => ColumnItf::PHP_TYPE_STRING,
                    'allow_null'     => true,
                    'auto_increment' => null,
                    'default_val'    => null,
                    'unsigned'       => null,
                    'min_length'     => 0,
                    'max_length'     => 16777215,
                    'min_value'      => null,
                    'max_value'      => null,
                    'valid_values'   => null,
                ],
            ],
            // XX --------------------------------------------------------------------
        ];
    }

    /**
     * @covers ::parseMySQLType
     *
     * @expectedException \Kicaj\Schema\SchemaException
     * @expectedExceptionMessage Could not parse type: 1int(11)
     */
    public function test_parseMySQLType_couldNotParse()
    {
        // When
        $columnDef = " `id` 1int(11) unsigned NOT NULL AUTO_INCREMENT,";

        // Then
        new Column($columnDef, 1, $this->tableMock);
    }

    /**
     * @covers ::parseMySQLType
     *
     * @expectedException \Kicaj\Schema\SchemaException
     * @expectedExceptionMessage Unsupported type: sometype
     */
    public function test_parseMySQLType_unsupportedType()
    {
        // When
        $columnDef = " `id` sometype(11) unsigned NOT NULL AUTO_INCREMENT,";

        // Then
        new Column($columnDef, 2, $this->tableMock);
    }

    /**
     * @covers ::parseAndSetColExtra
     *
     * @expectedException \Kicaj\Schema\SchemaException
     * @expectedExceptionMessage Could not decipher DEFAULT: unsigned DEFAULT: ---
     */
    public function test_parseAndSetColExtra_badDefault()
    {
        // When
        $columnDef = "`col1` int(11) unsigned DEFAULT: ---";

        // Then
        new Column($columnDef, 3, $this->tableMock);
    }

    /**
     * @covers ::setPartOfPk
     * @covers ::isPartOfPk
     */
    public function test_setPartOfPk()
    {
        // Given
        $columnDef = "  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,";
        $column = new Column($columnDef, 3, $this->tableMock);

        // When
        $column->setPartOfPk();

        // Then
        $this->assertTrue($column->isPartOfPk());
    }
}
