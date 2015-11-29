## Class Kicaj\SchemaDump\SchemaDump
SchemaDump.
Dumps MySQL database tables CREATE statements to a file.

There are two dump modes:
 - as PHP array - creates includable PHP file with $createStatements
   associative array where keys are table names and values are SQL CREATE statements.
 - as SQL statements - creates file with CREATE statements for all tables in given database.

This tool not only dumps CREATE statements but rewrites it in following way:
 - resets AUTO_INCREMENT to 1
 - adds CREATE TABLE IF NOT EXISTS (configurable)
 - adds DROP TABLE IF EXISTS (configurable)
## Constants

```php
const FORMAT_PHP_FILE = 'phpFile';
const FORMAT_PHP_ARRAY = 'phpArray';
const FORMAT_SQL = 'sql';
const PHP_TYPE_INT = 'int';
const PHP_TYPE_STRING = 'string';
const PHP_TYPE_FLOAT = 'float';
const PHP_TYPE_BOOL = 'bool';
const PHP_TYPE_BINARY = 'binary';
const PHP_TYPE_ARRAY = 'array';
const PHP_TYPE_DATE = 'date';
const PHP_TYPE_DATETIME = 'datetime';
const PHP_TYPE_TIMESTAMP = 'timestamp';
const PHP_TYPE_TIME = 'time';
const PHP_TYPE_YEAR = 'year';
const COLUMN_UNSIGNED = 'unsigned';
const COLUMN_NOT_NULL = 'not null';
const COLUMN_AUTOINCREMENT = 'autoincrement';
```

## Methods

|                                              |                                              |                                              |                                              |
| -------------------------------------------- | -------------------------------------------- | -------------------------------------------- | -------------------------------------------- |
|         [__construct](#__construct)          |                [make](#make)                 |       [isValidFormat](#isvalidformat)        | [getCreateStatements](#getcreatestatements)  |

## Properties

|                      |                      |
| -------------------- | -------------------- |
|  [$config](#config)  |   [$dbDrv](#dbdrv)   |

-------

#### $config
Schema dump configuration.

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
- _$dbConfig_ **array** - The database configuration

-------
#### make
Make.
```php
public static function make(array $dbConfig) : Kicaj\SchemaDump\SchemaDump
```
Arguments:
- _$dbConfig_ **array** - The database configuration

Returns: **[Kicaj\SchemaDump\SchemaDump](Kicaj-SchemaDump-SchemaDump.md)**

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
When returning array the keys are table names and values are an
array with keys:

create - CREATE TABLE or VIEW statement
drop   - DROP TABLE statement
type   - table, view ( one of the self::CREATE_TYPE_* )
name   - table name


Throws:
- [Kicaj\SchemaDump\SchemaException](Kicaj-SchemaDump-SchemaException.md)

Returns: **array|string**

-------
