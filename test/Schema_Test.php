<?php

namespace Kicaj\Test\Schema;

use Kicaj\Schema\Db;
use Kicaj\Schema\Schema;
use Kicaj\Tools\Helper\Str;

/**
 * Schema tests.
 *
 * @coversDefaultClass \Kicaj\Schema\Schema
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Schema_Test extends BaseTest
{
    /**
     * @var Schema
     */
    protected $schema;

    public function setUp()
    {
        Db::_resetInstances();
        $this->schema = Schema::make($this->getSchemaConfig('SCHEMA1'));
    }

    /**
     * @covers ::make
     * @covers ::__construct
     */
    public function test___construct()
    {
        // When
        $se = Schema::make($this->getSchemaConfig('SCHEMA1'));

        // Then
        $this->assertInstanceOf('\Kicaj\Schema\Schema', $se);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \Kicaj\Schema\SchemaException
     * @expectedExceptionMessageRegExp /Access denied for user/
     */
    public function test___construct_error()
    {
        // When
        $dbConfig = $this->getSchemaConfig('SCHEMA1');
        $dbConfig['connection']['password'] = 'wrongOne';

        // Then
        Schema::make($dbConfig);
    }

    /**
     * @dataProvider isValidFormatProvider
     *
     * @covers ::isValidFormat
     *
     * @param string $format
     * @param string $expected
     */
    public function test_isValidFormat($format, $expected)
    {
        // When
        $isValid = $this->schema->isValidFormat($format);

        // Then
        $this->assertSame($expected, $isValid);
    }

    public function isValidFormatProvider()
    {
        return [
            [Schema::FORMAT_PHP_ARRAY, true],
            [Schema::FORMAT_PHP_FILE, true],
            [Schema::FORMAT_SQL, true],
            ['', false],
            ['aa', false],
        ];
    }

    /**
     * @covers ::getCreateStatements
     */
    public function test_getCreateStatements_phpArray()
    {
        // Given
        $this->dbDropAllTables('SCHEMA1');
        $this->dbDropAllViews('SCHEMA1');
        $this->dbLoadFixtures('SCHEMA1', ['test1.sql', 'view.sql']);

        // When
        $gotDef = $this->schema->getCreateStatements();
        $gotStmt = $this->schema->getCreateStatements(Schema::FORMAT_PHP_ARRAY);

        // Then
        $this->assertSame($gotDef, $gotStmt);
        $this->assertSame(2, count(array_keys($gotStmt)));
        $this->assertArrayHasKey('test1', $gotStmt);
        $this->assertArrayHasKey('my_view', $gotStmt);
    }

    /**
     * @covers ::getCreateStatements
     */
    public function test_getCreateStatements_phpFile()
    {
        // Given
        $this->dbDropAllTables('SCHEMA1');
        $this->dbDropAllViews('SCHEMA1');
        $this->dbLoadFixtures('SCHEMA1', ['test1.sql', 'view.sql']);

        // When
        $dbConfig = $this->getSchemaConfig('SCHEMA1');
        $dbConfig['add_if_not_exists'] = true;
        $gotStmt = Schema::make($dbConfig)->getCreateStatements(Schema::FORMAT_PHP_FILE);

        // Then
        $this->assertSame($this->getFixtureData('test1_and_view.txt'), $gotStmt);
    }

    /**
     * @covers ::getCreateStatements
     */
    public function test_getCreateStatements_sql()
    {
        // Given
        $this->dbDropAllTables('SCHEMA1');
        $this->dbDropAllViews('SCHEMA1');
        $this->dbLoadFixtures('SCHEMA1', ['test1.sql', 'view.sql']);

        // When
        $gotStmt = $this->schema->getCreateStatements(Schema::FORMAT_SQL);

        // Then
        $expected = $this->getFixtureRawData('test1_and_view.sql');
        $this->assertSame($expected, $gotStmt);
    }

    /**
     * @covers ::getCreateStatements
     *
     * @expectedException \Kicaj\Schema\SchemaException
     * @expectedExceptionMessage unknown format: unknown
     */
    public function test_getCreateStatements_unknown()
    {
        $this->schema->getCreateStatements('unknown');
    }
}
