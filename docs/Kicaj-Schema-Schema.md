## Class Kicaj\Schema\Schema
Schema.
Exports MySQL database tables CREATE statements to a file.

There are two export modes:
 - as PHP array      - creates includable PHP file with $createStatements associative array
                       where keys are table names and values are SQL CREATE statements.
 - as SQL statements - creates file with CREATE statements for all tables in given database.

This tool not only exports CREATE statements but rewrites it in following way:
 - resets AUTO_INCREMENT to 1
 - adds CREATE TABLE IF NOT EXISTS (configurable)
 - adds DROP TABLE IF EXISTS (configurable)
## Constants

```php
const FORMAT_PHP_FILE = 'php_file';
const FORMAT_PHP_ARRAY = 'php_array';
const FORMAT_SQL = 'sql';
const CONFIG_KEY_CONNECTION = 'connection';
const CONFIG_KEY_EXPORT_FORMAT = 'export_format';
const CONFIG_KEY_AINE = 'add_if_not_exists';
const CONFIG_KEY_OUTPUT_FILE = 'output_file';
```

## Methods

|                                              |                                              |                                              |                                              |
| -------------------------------------------- | -------------------------------------------- | -------------------------------------------- | -------------------------------------------- |
|         [__construct](#__construct)          |                [make](#make)                 |       [isValidFormat](#isvalidformat)        | [getCreateStatements](#getcreatestatements)  |

## Properties

|                      |                      |
| -------------------- | -------------------- |
|  [$config](#config)  |      [$db](#db)      |

-------

#### $config
Schema export configuration.

```php
protected array $config
```

-------
## Methods
#### __construct
Constructor.
```php
public function __construct(array $dbConfig) : 
```
Arguments:
- _$dbConfig_ **array** - The database configuration.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### make
Make.
```php
public static function make(array $dbConfig) : Kicaj\Schema\Schema
```
Arguments:
- _$dbConfig_ **array** - The database configuration

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **[Kicaj\Schema\Schema](Kicaj-Schema-Schema.md)**

-------
#### isValidFormat
Returns true for valid format.
```php
public static function isValidFormat(string $format) : boolean
```
Arguments:
- _$format_ **string** - The format of the file: self::FORMAT_*

Returns: **boolean**

-------
#### getCreateStatements
Get create statements string.
```php
public function getCreateStatements(string $format) : array|string
```
Arguments:
- _$format_ **string** - The format of the file: self::FORMAT_*

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **array|string**

-------
