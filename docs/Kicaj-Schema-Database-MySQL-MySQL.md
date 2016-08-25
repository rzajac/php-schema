## Class Kicaj\Schema\Database\MySQL\MySQL
MySQL database driver.

## Implements

- Kicaj\DbKit\DbConnector
- [Kicaj\Schema\Itf\DatabaseItf](Kicaj-Schema-Itf-DatabaseItf.md)

## Constants

```php
const TYPE_TINYINT = 'tinyint';
const TYPE_SMALLINT = 'smallint';
const TYPE_MEDIUMINT = 'mediumint';
const TYPE_INT = 'int';
const TYPE_BIGINT = 'bigint';
const TYPE_DECIMAL = 'decimal';
const TYPE_BIT = 'bit';
const TYPE_BINARY = 'binary';
const TYPE_VARBINARY = 'varbinary';
const TYPE_CHAR = 'char';
const TYPE_VARCHAR = 'varchar';
const TYPE_TEXT = 'text';
const TYPE_TINYTEXT = 'tinytext';
const TYPE_LONGTEXT = 'longtext';
const TYPE_MEDIUMTEXT = 'mediumtext';
const TYPE_BLOB = 'blob';
const TYPE_LONGBLOB = 'longblob';
const TYPE_MEDIUMBLOB = 'mediumblob';
const TYPE_TINYBLOB = 'tinyblob';
const TYPE_FLOAT = 'float';
const TYPE_DOUBLE = 'double';
const TYPE_ENUM = 'enum';
const TYPE_SET = 'set';
const TYPE_TIMESTAMP = 'timestamp';
const TYPE_DATETIME = 'datetime';
const TYPE_DATE = 'date';
const TYPE_TIME = 'time';
const TYPE_YEAR = 'year';
```

## Methods

|                                                |                                                |                                                |                                                |
| ---------------------------------------------- | ---------------------------------------------- | ---------------------------------------------- | ---------------------------------------------- |
|              [dbSetup](#dbsetup)               |            [dbConnect](#dbconnect)             |              [dbClose](#dbclose)               | [getTableAndViewNames](#gettableandviewnames)  |
|      [dbGetTableNames](#dbgettablenames)       |       [dbGetViewNames](#dbgetviewnames)        | [dbGetTableDefinition](#dbgettabledefinition)  |             [runQuery](#runquery)              |
|         [getRowsArray](#getrowsarray)          |                     [](#)                      |                     [](#)                      |                     [](#)                      |

## Properties

|                          |                          |                          |
| ------------------------ | ------------------------ | ------------------------ |
|    [$dbName](#dbname)    |    [$mysqli](#mysqli)    |  [$dbConfig](#dbconfig)  |

-------

#### $dbName
The database name.

```php
protected string $dbName
```

#### $mysqli
The database driver.

```php
protected \mysqli $mysqli
```

#### $dbConfig
Database configuration.

```php
protected array $dbConfig
```

-------
## Methods
#### dbSetup

```php
public function dbSetup() : 
```

-------
#### dbConnect

```php
public function dbConnect() : 
```

-------
#### dbClose

```php
public function dbClose() : 
```

-------
#### getTableAndViewNames
Return table and view names form the database.
```php
protected function getTableAndViewNames() : array
```

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **array**

-------
#### dbGetTableNames
Get database table names.
```php
public function dbGetTableNames() : 
```

-------
#### dbGetViewNames
Get database view names.
```php
public function dbGetViewNames() : 
```

-------
#### dbGetTableDefinition
Return table definition for given database table.
```php
public function dbGetTableDefinition() : 
```

-------
#### runQuery
Run SQL query.
```php
public function runQuery(string $sql) : boolean|\mysqli_result
```
Arguments:
- _$sql_ **string** - The SQL query.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **boolean|\mysqli_result**

-------
#### getRowsArray
Get all rows from the DB result.
```php
public function getRowsArray(mysqli_result|boolean $result) : array
```
Arguments:
- _$result_ **mysqli_result|boolean** - The return of query method.

Returns: **array** - The array of SQL rows

-------
