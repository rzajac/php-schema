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

use Kicaj\Schema\Database\SchemaFactory;
use Kicaj\Test\Schema\BaseTest;
use Kicaj\DbKit\DatabaseException;
use Kicaj\DbKit\DbConnector;
use Kicaj\Tools\Exception;

/**
 * SchemaFactory tests.
 *
 * @coversDefaultClass Kicaj\Schema\Database\SchemaFactory
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class SchemaFactory_Test extends BaseTest
{
    /**
     * @dataProvider factoryProvider
     *
     * @covers ::factory
     *
     * @param string $driverName
     * @param string $expMsg
     */
    public function test_factory($driverName, $expMsg)
    {
        $dbConfig = self::getSchemaConfig('SCHEMA1');
        $dbConfig['connection']['driver'] = $driverName;

        try {
            $mysql = SchemaFactory::factory($dbConfig);

            $hasThrown = false;
            $this->assertInstanceOf('\Kicaj\Schema\SchemaGetter', $mysql);
        } catch (Exception $e) {
            $hasThrown = true;
            $this->assertContains($expMsg, $e->getMessage());
        }

        if ($expMsg) {
            $this->assertTrue($hasThrown);
        } else {
            $this->assertFalse($hasThrown);
        }
    }

    public function factoryProvider()
    {
        return [
            [DbConnector::DB_DRIVER_MYSQL, ''],
            ['unknown', 'unknown database driver name: unknown'],
        ];
    }

    /**
     * @covers ::factory
     */
    public function test_factoryConnErr()
    {
        // Given
        SchemaFactory::_resetInstances();

        // When
        $dbConfig = self::getSchemaConfig('SCHEMA1');
        $dbConfig['connection']['password'] = 'wrongOne';

        try {
            SchemaFactory::factory($dbConfig, true);
            $thrown = false;
            $errMsg = '';
        } catch (DatabaseException $e) {
            $thrown = true;
            $errMsg = $e->getMessage();
        }

        // Then
        $this->assertTrue($thrown);
        $this->assertContains('Access denied for user', $errMsg);
    }

    /**
     * @covers ::factory
     */
    public function test_factory_sameInstance()
    {
        // When
        $dbConfig = self::getSchemaConfig('SCHEMA1');

        $db1 = SchemaFactory::factory($dbConfig);
        $db2 = SchemaFactory::factory($dbConfig);

        // Then
        $this->assertSame($db1, $db2);
    }
}
