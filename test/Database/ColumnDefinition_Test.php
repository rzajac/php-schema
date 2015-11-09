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

namespace Kicaj\Test\SchemaDump\Database;

use Kicaj\SchemaDump\ColumnDefinition;
use Kicaj\Tools\Db\DbConnector;

/**
 * ColumnDefinition_Test.
 *
 * @coversDefaultClass \Kicaj\SchemaDump\ColumnDefinition
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class ColumnDefinition_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::make
     * @covers ::getName
     * @covers ::getTableName
     * @covers ::getDriverName
     */
    public function test___construct_()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $this->assertInstanceOf('\Kicaj\SchemaDump\ColumnDefinition', $cd);
        $this->assertSame('', $cd->getPhpType());
        $this->assertSame(false, $cd->isUnsigned());
        $this->assertSame(false, $cd->isNotNull());
        $this->assertSame(false, $cd->isAutoincrement());
        $this->assertSame(false, $cd->isPartOfPk());
        $this->assertSame('testColumn', $cd->getName());
        $this->assertSame('testTable', $cd->getTableName());
        $this->assertSame(null, $cd->getDefaultValue());
        $this->assertSame(null, $cd->getMinValue());
        $this->assertSame(null, $cd->getMaxValue());
        $this->assertSame(null, $cd->getMinLength());
        $this->assertSame(null, $cd->getMaxLength());
        $this->assertSame(DbConnector::DB_DRIVER_MYSQL, $cd->getDriverName());
        $this->assertSame('', $cd->getDbType());
        $this->assertSame(null, $cd->getValidValues());
    }

    /**
     * @covers ::getPhpType
     * @covers ::setPhpType
     */
    public function test_getPhpType()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setPhpType('testType');
        $this->assertSame('testType', $cd->getPhpType());
    }

    /**
     * @covers ::isUnsigned
     * @covers ::setIsUnsigned
     */
    public function test_isIsUnsigned()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setIsUnsigned(true);
        $this->assertTrue($cd->isUnsigned());
    }

    /**
     * @covers ::isNotNull
     * @covers ::setNotNull
     */
    public function test_isNotNull()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setNotNull(true);
        $this->assertTrue($cd->isNotNull());
    }

    /**
     * @covers ::isAutoincrement
     * @covers ::setIsAutoincrement
     */
    public function test_isIsAutoincrement()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setIsAutoincrement(true);
        $this->assertTrue($cd->isAutoincrement());
    }

    /**
     * @covers ::isPartOfPk
     * @covers ::setIsPartOfPk
     */
    public function test_isPartOfPk()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setIsPartOfPk(true);
        $this->assertTrue($cd->isPartOfPk());
    }

    /**
     * @covers ::getDefaultValue
     * @covers ::setDefaultValue
     */
    public function test_getDefaultValue()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setDefaultValue('default');
        $this->assertSame('default', $cd->getDefaultValue());
    }

    /**
     * @covers ::getMinValue
     * @covers ::setMinValue
     */
    public function test_getMinValue()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setMinValue(123);
        $this->assertSame(123, $cd->getMinValue());
    }

    /**
     * @covers ::getMaxValue
     * @covers ::setMaxValue
     */
    public function test_getMaxValue()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setMaxValue(123);
        $this->assertSame(123, $cd->getMaxValue());
    }

    /**
     * @covers ::getMinLength
     * @covers ::setMinLength
     */
    public function test_getMinLength()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setMinLength(123);
        $this->assertSame(123, $cd->getMinLength());
    }

    /**
     * @covers ::getMaxLength
     * @covers ::setMaxLength
     */
    public function test_getMaxLength()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setMaxLength(123);
        $this->assertSame(123, $cd->getMaxLength());
    }

    /**
     * @covers ::getDbType
     * @covers ::setDbType
     */
    public function test_getDbType()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setDbType('dbType');
        $this->assertSame('dbType', $cd->getDbType());
    }

    /**
     * @covers ::getValidValues
     * @covers ::setValidValues
     */
    public function test_getValidValues()
    {
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        $cd->setValidValues(['dbType', 'dbType1']);
        $this->assertSame(['dbType', 'dbType1'], $cd->getValidValues());
    }
}
