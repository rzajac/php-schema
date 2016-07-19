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
use Kicaj\Schema\Schema;
use Kicaj\Tools\Db\DbConnector;

/**
 * ColumnDefinition_Test.
 *
 * @coversDefaultClass \Kicaj\Schema\ColumnDefinition
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
    public function test___construct()
    {
        // When
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // Then
        $this->assertInstanceOf('\Kicaj\Schema\ColumnDefinition', $cd);
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
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setPhpType('testType');

        // Then
        $this->assertSame('testType', $cd->getPhpType());
    }

    /**
     * @covers ::isUnsigned
     * @covers ::setIsUnsigned
     */
    public function test_isIsUnsigned()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setIsUnsigned(true);

        // Then
        $this->assertTrue($cd->isUnsigned());
    }

    /**
     * @covers ::isNotNull
     * @covers ::setNotNull
     */
    public function test_isNotNull()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setNotNull(true);

        // Then
        $this->assertTrue($cd->isNotNull());
    }

    /**
     * @covers ::isAutoincrement
     * @covers ::setIsAutoincrement
     */
    public function test_isIsAutoincrement()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setIsAutoincrement(true);

        // Then
        $this->assertTrue($cd->isAutoincrement());
    }

    /**
     * @covers ::isPartOfPk
     * @covers ::setIsPartOfPk
     */
    public function test_isPartOfPk()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setIsPartOfPk(true);

        // Then
        $this->assertTrue($cd->isPartOfPk());
    }

    /**
     * @dataProvider setDefaultValueProvider
     *
     * @covers ::getDefaultValue
     * @covers ::setDefaultValue
     * @covers ::setPhpType
     *
     * @param mixed  $defaultValue
     * @param string $phpType The one of the Schema::PHP_TYPE_* constants.
     * @param mixed  $expected
     */
    public function test_setDefaultValue($defaultValue, $phpType, $expected)
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setPhpType($phpType)->setDefaultValue($defaultValue);

        // Then
        $this->assertSame($expected, $cd->getDefaultValue());
    }

    public function setDefaultValueProvider()
    {
        return [
            [null, 'does_not_matter', null],
            [123, Schema::PHP_TYPE_INT, 123],
            ['123', Schema::PHP_TYPE_INT, 123],
            ['123', Schema::PHP_TYPE_FLOAT, 123.0],
            ['123.45', Schema::PHP_TYPE_FLOAT, 123.45],
            ['true', Schema::PHP_TYPE_BOOL, true],
            [true, Schema::PHP_TYPE_BOOL, true],
            [1, Schema::PHP_TYPE_BOOL, true],

            [false, Schema::PHP_TYPE_BOOL, false],
            [0, Schema::PHP_TYPE_BOOL, false],
            [0.0, Schema::PHP_TYPE_BOOL, false],
            ['', Schema::PHP_TYPE_BOOL, false],
            [[], Schema::PHP_TYPE_BOOL, false],
            [null, Schema::PHP_TYPE_BOOL, false],

            [null, Schema::PHP_TYPE_ARRAY, null],
        ];
    }


    /**
     * @covers ::getDefaultValue
     * @covers ::setDefaultValue
     */
    public function test_getDefaultValue()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setDefaultValue('default');

        // Then
        $this->assertSame('default', $cd->getDefaultValue());
    }

    /**
     * @covers ::getMinValue
     * @covers ::setMinValue
     */
    public function test_getMinValue()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setMinValue(123);

        // Then
        $this->assertSame(123, $cd->getMinValue());
    }

    /**
     * @covers ::getMaxValue
     * @covers ::setMaxValue
     */
    public function test_getMaxValue()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setMaxValue(123);

        // Then
        $this->assertSame(123, $cd->getMaxValue());
    }

    /**
     * @covers ::getMinLength
     * @covers ::setMinLength
     */
    public function test_getMinLength()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setMinLength(123);

        // Then
        $this->assertSame(123, $cd->getMinLength());
    }

    /**
     * @covers ::getMaxLength
     * @covers ::setMaxLength
     */
    public function test_getMaxLength()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setMaxLength(123);

        // Then
        $this->assertSame(123, $cd->getMaxLength());
    }

    /**
     * @covers ::getDbType
     * @covers ::setDbType
     */
    public function test_getDbType()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setDbType('dbType');

        // Then
        $this->assertSame('dbType', $cd->getDbType());
    }

    /**
     * @covers ::getValidValues
     * @covers ::setValidValues
     */
    public function test_getValidValues()
    {
        // Given
        $cd = ColumnDefinition::make('testColumn', DbConnector::DB_DRIVER_MYSQL, 'testTable');

        // When
        $cd->setValidValues(['dbType', 'dbType1']);

        // Then
        $this->assertSame(['dbType', 'dbType1'], $cd->getValidValues());
    }
}
