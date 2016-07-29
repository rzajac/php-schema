<?php

namespace Kicaj\Test\Schema;

use Kicaj\Schema\Database\SchemaFactory;
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
        $this->schema = Schema::make(self::getSchemaConfig('SCHEMA1'));
    }

    /**
     * @covers ::make
     * @covers ::__construct
     */
    public function test___construct()
    {
        // Given
        SchemaFactory::_resetInstances();

        // When
        $se = Schema::make(self::getSchemaConfig('SCHEMA1'));

        // Then
        $this->assertInstanceOf('\Kicaj\Schema\Schema', $se);
    }

    /**
     * @covers ::__construct
     *
     * @expectedException \Kicaj\DbKit\DatabaseException
     * @expectedExceptionMessageRegExp /Access denied for user/
     */
    public function test___construct_error()
    {
        // Given
        SchemaFactory::_resetInstances();

        // When
        $dbConfig = self::getSchemaConfig('SCHEMA1');
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
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', 'test1.sql');

        // When
        $gotDef = $this->schema->getCreateStatements();
        $gotStmt = $this->schema->getCreateStatements(Schema::FORMAT_PHP_ARRAY);

        // Then
        $this->assertSame($gotDef, $gotStmt);
        $this->assertSame(1, count(array_keys($gotStmt)));
        $this->assertArrayHasKey('test1', $gotStmt);
    }

    /**
     * @covers ::getCreateStatements
     */
    public function test_getCreateStatements_phpFile()
    {
        // Given
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', 'test1.sql');

        // When
        $dbConfig = self::getSchemaConfig('SCHEMA1');
        $dbConfig['add_if_not_exists'] = true;
        $gotStmt = Schema::make($dbConfig)->getCreateStatements(Schema::FORMAT_PHP_FILE);

        // Then
        $this->assertSame(self::getFixtureData('test1.txt'), $gotStmt);
    }

    /**
     * @covers ::getCreateStatements
     */
    public function test_getCreateStatements_sql()
    {
        // Given
        self::dbDropAllTables('SCHEMA1');
        self::dbLoadFixtures('SCHEMA1', 'test1.sql');

        // When
        $gotStmt = $this->schema->getCreateStatements(Schema::FORMAT_SQL);
        $gotStmt = Str::oneLine($gotStmt);

        // Then
        $expected = self::getFixtureData('test1.sql')[0];
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
