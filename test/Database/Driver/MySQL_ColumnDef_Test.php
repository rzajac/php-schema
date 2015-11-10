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

namespace Kicaj\Test\SchemaDump\Database\Driver;

use Kicaj\SchemaDump\ColumnDefinition;
use Kicaj\SchemaDump\Database\Driver\MySQL;
use Kicaj\SchemaDump\SchemaDump;
use Kicaj\Tools\Db\DbConnector;

/**
 * MySQL_ColumnDef_Test.
 *
 * @coversDefaultClass \Kicaj\SchemaDump\Database\Driver\MySQL
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class MySQL_ColumnDef_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_int_unsigned_notNull_autoincrement()
    {
        $colDefStr = '  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('id', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_INT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(4294967295, $colDef->getMaxValue());

        $this->assertTrue($colDef->isUnsigned());

        $this->assertTrue($colDef->isAutoincrement());
        $this->assertTrue($colDef->isNotNull());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_int_notNull_autoincrement()
    {
        $colDefStr = '  `id` int(11) NOT NULL AUTO_INCREMENT,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('id', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_INT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(-2147483648, $colDef->getMinValue());
        $this->assertSame(2147483647, $colDef->getMaxValue());

        $this->assertFalse($colDef->isUnsigned());
        $this->assertTrue($colDef->isAutoincrement());
        $this->assertTrue($colDef->isNotNull());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_int_unsigned_notNull()
    {
        $colDefStr = '  `t1_id` int(10) unsigned NOT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('t1_id', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_INT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(4294967295, $colDef->getMaxValue());

        $this->assertTrue($colDef->isUnsigned());
        $this->assertTrue($colDef->isNotNull());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_int_unsigned_notNull_default()
    {
        $colDefStr = '`col1` int(11) unsigned NOT NULL DEFAULT \'123\',';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('col1', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_INT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(4294967295, $colDef->getMaxValue());

        $this->assertTrue($colDef->isUnsigned());
        $this->assertTrue($colDef->isNotNull());

        $this->assertSame('123', $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_int_unsigned_default_null()
    {
        $colDefStr = '`col1` int(11) unsigned DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('col1', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_INT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(4294967295, $colDef->getMaxValue());

        $this->assertTrue($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_tinyint_default_null()
    {
        $colDefStr = '  `tint` tinyint(4) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('tint', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_TINYINT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(-128, $colDef->getMinValue());
        $this->assertSame(127, $colDef->getMaxValue());

        $this->assertFalse($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_tinyint_unsigned_default_null()
    {
        $colDefStr = '  `tint` tinyint(4) unsigned DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('tint', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_TINYINT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(255, $colDef->getMaxValue());

        $this->assertTrue($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     *
     * @expectedException \Kicaj\SchemaDump\SchemaException
     * @expectedExceptionMessageRegExp /could not decipher DEFAULT: /
     */
    public function test_parseColumn_int_unsigned_default_error()
    {
        $colDefStr = '`col1` int(11) unsigned DEFAULT a';
        MySQL::parseColumn($colDefStr, 'testTable');
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_char_default_null()
    {
        $colDefStr = '  `chr` char(1) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('chr', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_CHAR, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(1, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_vchar_default_null()
    {
        $colDefStr = '  `vchr` varchar(2) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('vchr', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_VARCHAR, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(2, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_smallint_default_null()
    {
        $colDefStr = '  `sint` smallint(6) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('sint', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_SMALLINT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(-32768, $colDef->getMinValue());
        $this->assertSame(32767, $colDef->getMaxValue());

        $this->assertFalse($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_smallint_unsigned_default_null()
    {
        $colDefStr = '  `sint` smallint(6) unsigned DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('sint', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_SMALLINT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(65535, $colDef->getMaxValue());

        $this->assertTrue($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_mediumint_default_null()
    {
        $colDefStr = '  `mint` mediumint(9) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('mint', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_MEDIUMINT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(-8388608, $colDef->getMinValue());
        $this->assertSame(8388607, $colDef->getMaxValue());

        $this->assertFalse($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_mediumint_unsigned_default_null()
    {
        $colDefStr = '  `mint` mediumint(9) unsigned DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('mint', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_MEDIUMINT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(16777215, $colDef->getMaxValue());

        $this->assertTrue($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_integer_default_null()
    {
        $colDefStr = '  `inte` int(11) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('inte', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_INT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(-2147483648, $colDef->getMinValue());
        $this->assertSame(2147483647, $colDef->getMaxValue());

        $this->assertFalse($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_integer_unsigned_default_null()
    {
        $colDefStr = '  `inte` int(11) unsigned DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('inte', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_INT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(4294967295, $colDef->getMaxValue());

        $this->assertTrue($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_bigint_default_null()
    {
        $colDefStr = '  `bint` bigint(20) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('bint', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_BIGINT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(-9223372036854775808, $colDef->getMinValue());
        $this->assertSame(9223372036854775807, $colDef->getMaxValue());

        $this->assertFalse($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_bigint_unsigned_default_null()
    {
        $colDefStr = '  `bint` bigint(20) unsigned DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('bint', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_BIGINT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(18446744073709551615, $colDef->getMaxValue());

        $this->assertTrue($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_float_default_null()
    {
        $colDefStr = '  `flt` float DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('flt', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_FLOAT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_FLOAT, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isUnsigned());
        $this->assertFalse($colDef->isNotNull());

        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_double_default_null()
    {
        $colDefStr = '  `doub` double DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('doub', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_FLOAT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_DOUBLE, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_decimal_default_null()
    {
        $colDefStr = '  `deci` decimal(10,0) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('deci', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_INT, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_DECIMAL, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_bit_default_null()
    {
        $colDefStr = '  `bt` bit(1) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('bt', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_BINARY, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_BIT, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(1, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_tinytext_default_null()
    {
        $colDefStr = '  `ttxt` tinytext,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('ttxt', $colDef->getName());
        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_TINYTEXT, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(255, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_txt_default_null()
    {
        $colDefStr = '  `txt` text,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('txt', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_TEXT, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(65535, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_mediumtext_default_null()
    {
        $colDefStr = '  `mtxt` mediumtext,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('mtxt', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_MEDIUMTEXT, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(16777215, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_longtext_default_null()
    {
        $colDefStr = '  `ltxt` longtext,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('ltxt', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_LONGTEXT, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(4294967295, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_tinyblob_default_null()
    {
        $colDefStr = '  `tblob` tinyblob,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('tblob', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_TINYBLOB, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(255, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_mediumblob_default_null()
    {
        $colDefStr = '  `mblob` mediumblob,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('mblob', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_MEDIUMBLOB, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(16777215, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_blob_default_null()
    {
        $colDefStr = '  `nblob` blob,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('nblob', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_BLOB, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(65535, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_longblob_default_null()
    {
        $colDefStr = '  `lblob` longblob,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('lblob', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_LONGBLOB, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(4294967295, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_binary_default_null()
    {
        $colDefStr = '  `bin` binary(1) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('bin', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_BINARY, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_BINARY, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(1, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_varbinary_default_null()
    {
        $colDefStr = '  `vbin` varbinary(20) DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('vbin', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_BINARY, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_VARBINARY, $colDef->getDbType());

        $this->assertSame(0, $colDef->getMinLength());
        $this->assertSame(20, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_date_default_null()
    {
        $colDefStr = '  `d` date DEFAULT \'2015-10-20\',';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('d', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_DATE, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_DATE, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame('1000-01-01', $colDef->getMinValue());
        $this->assertSame('9999-12-31', $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame('2015-10-20', $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_datetime_default_null()
    {
        $colDefStr = '  `dt` datetime DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('dt', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_DATETIME, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_DATETIME, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame('1000-01-01 00:00:00', $colDef->getMinValue());
        $this->assertSame('9999-12-31 23:59:59', $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_timestamp_default_null()
    {
        $colDefStr = '  `ts` timestamp NULL DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('ts', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_TIMESTAMP, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_TIMESTAMP, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(0, $colDef->getMinValue());
        $this->assertSame(2147483647, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_time_default_null()
    {
        $colDefStr = '  `tm` time DEFAULT NULL,';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('tm', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_TIME, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_TIME, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame(null, $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_year_default_null()
    {
        $colDefStr = '  `y` year(4) DEFAULT \'2015\',';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('y', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_YEAR, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_YEAR, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(1901, $colDef->getMinValue());
        $this->assertSame(2155, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame('2015', $colDef->getDefaultValue());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_enum()
    {
        $colDefStr = '  `e` enum(\'0\',\'XS\',\'s p a c e\') DEFAULT \'XS\',';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('e', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_ENUM, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame('XS', $colDef->getDefaultValue());

        $this->assertSame(['0', 'XS', 's p a c e'], $colDef->getValidValues());
    }

    /**
     * @covers ::parseColumn
     * @covers ::setColDefExtra
     * @covers ::mySQLToPhpType
     * @covers ::setTypeBounds
     * @covers ::mySQLTypeLengths
     */
    public function test_parseColumn_set()
    {
        $colDefStr = '  `s` set(\'0\',\'XS\',\'s p a c e\') DEFAULT \'0\',';
        $colDef = MySQL::parseColumn($colDefStr, 'testTable');

        $this->assertSame('s', $colDef->getName());

        $this->assertSame(SchemaDump::PHP_TYPE_STRING, $colDef->getPhpType());
        $this->assertSame(MySQL::TYPE_SET, $colDef->getDbType());

        $this->assertSame(null, $colDef->getMinLength());
        $this->assertSame(null, $colDef->getMaxLength());

        $this->assertSame(null, $colDef->getMinValue());
        $this->assertSame(null, $colDef->getMaxValue());

        $this->assertFalse($colDef->isNotNull());
        $this->assertSame('0', $colDef->getDefaultValue());

        $this->assertSame(['0', 'XS', 's p a c e'], $colDef->getValidValues());
    }

    /**
     * @covers ::setTypeBounds
     *
     * @expectedException \Kicaj\SchemaDump\SchemaException
     * @expectedExceptionMessage unknown database type: wrong type
     */
    public function test_setTypeBounds_error()
    {
        $colDef = new ColumnDefinition('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');
        $colDef->setDbType('wrong type');

        MySQL::setTypeBounds($colDef);
    }

    /**
     * @covers ::mySQLToPhpType
     *
     * @expectedException \Kicaj\SchemaDump\SchemaException
     * @expectedExceptionMessage unknown type:
     */
    public function test_mySQLToPhpType_error()
    {
        $colDef = new ColumnDefinition('bad column definition', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        MySQL::mySQLToPhpType($colDef, 'bad column definition');
    }

}
