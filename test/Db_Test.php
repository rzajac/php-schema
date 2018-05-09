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

namespace Kicaj\Test\Schema;


use Kicaj\Schema\Database\DbConnector;
use Kicaj\Schema\Db;
use Kicaj\Schema\SchemaEx;

/**
 * Db_Test.
 *
 * @coversDefaultClass \Kicaj\Schema\Db
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Db_Test extends BaseTest
{
    protected function setUp()
    {
        Db::_resetInstances();
    }

    /**
     * @dataProvider factoryProvider
     *
     * @covers ::factory
     * @covers ::__construct
     *
     * @param string $driverName
     * @param string $errorMsg
     */
    public function test_factory($driverName, $errorMsg)
    {
        // Given
        $thrown = false;

        $dbConfig = $this->getSchemaConfig('SCHEMA1');
        $dbConfig['connection']['driver'] = $driverName;

        // Then
        try {
            $db = Db::factory($dbConfig);
            // Call method that is actually doing something with database.
            $db->dbGetTableNames();
        } catch (SchemaEx $e) {
            $thrown = true;
            $this->assertFalse('' == $errorMsg, 'Did not expect to see error: ' . $e->getMessage());
        } finally {
            $this->assertFalse('' !== $errorMsg && false === $thrown, 'Expected to see error: ' . $errorMsg);
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
     *
     * @expectedException \Kicaj\Schema\SchemaEx
     * @expectedExceptionMessage Access denied for user
     */
    public function test_factoryConnErr()
    {
        // Given
        $dbConfig = self::getSchemaConfig('SCHEMA1');
        $dbConfig['connection']['password'] = 'wrongOne';

        // When
        $db = Db::factory($dbConfig);

        // Then
        // Call method that is actually doing something with database.
        $db->dbGetTableNames();
    }

    /**
     * @covers ::factory
     *
     * @throws SchemaEx
     */
    public function test_factory_sameInstance()
    {
        // When
        $dbConfig = self::getSchemaConfig('SCHEMA1');

        $db1 = Db::factory($dbConfig);
        $db2 = Db::factory($dbConfig);

        // Then
        $this->assertSame($db1, $db2);
    }

    /**
     * @covers ::dbGetTableNames
     *
     * @throws SchemaEx
     *
     * @throws \Kicaj\Test\Helper\Database\DatabaseEx
     *
     * @return Db
     */
    public function test_dbGetTableNames_empty()
    {
        // Given
        $this->dbDropAllTables('SCHEMA1');
        $db = Db::factory(self::getSchemaConfig('SCHEMA1'));

        // When
        $tableNames = $db->dbGetTableNames();

        // Then
        $this->assertSame([], $tableNames);

        return $db;
    }

    /**
     * @covers ::dbGetTableNames
     *
     * @depends test_dbGetTableNames_empty
     *
     * @param Db $db
     *
     * @throws SchemaEx
     * @throws \Kicaj\Test\Helper\Database\DatabaseEx
     * @throws \Kicaj\Test\Helper\Loader\FixtureLoaderEx
     *
     * @return Db
     */
    public function test_dbGetTableNames_results($db)
    {
        // Given
        $this->dbLoadFixtures('SCHEMA1', ['table2.sql', 'table3.sql']);

        // When
        $tableNames = $db->dbGetTableNames();

        // Then
        $this->assertSame(['table2', 'table3'], $tableNames);

        return $db;
    }

    /**
     * @covers ::dbGetTableDefinition
     *
     * @depends test_dbGetTableNames_results
     *
     * @param Db $db
     *
     * @throws SchemaEx
     */
    public function test_dbGetTableDefinition_result($db)
    {
        // Given
        // When
        $table = $db->dbGetTableDefinition('table2');

        // Then
        $this->assertSame('table2', $table->getName());
    }

    /**
     * @covers ::dbGetTableNames
     *
     * @expectedException \Kicaj\Schema\SchemaEx
     * @expectedExceptionMessage Table 'testSchemaLib.not_existing' doesn't exist
     */
    public function test_dbGetTableDefinition_error()
    {
        // Given
        $db = Db::factory(self::getSchemaConfig('SCHEMA1'));

        // Then
        $db->dbGetTableDefinition('not_existing');
    }
}
