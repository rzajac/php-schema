## Interface Kicaj\Schema\SchemaGetter
Get database schema interface.

## Extends

- Kicaj\Tools\Db\DbConnector

## Constants

```php
const CREATE_TYPE_TABLE = 'table';
const CREATE_TYPE_VIEW = 'view';
const CREATE_TYPE_NONE = '';
```

## Methods

|                                                  |                                                  |                                                  |
| ------------------------------------------------ | ------------------------------------------------ | ------------------------------------------------ |
|       [dbGetTableNames](#dbgettablenames)        | [dbGetTableDropCommand](#dbgettabledropcommand)  |  [dbGetCreateStatement](#dbgetcreatestatement)   |
|  [dbGetTableDefinition](#dbgettabledefinition)   | [dbGetCreateStatements](#dbgetcreatestatements)  |                      [](#)                       |

-------
## Methods
#### dbGetTableNames
Get database table names.
```php
public function dbGetTableNames() : string[]
```

Throws:
- Kicaj\Tools\Db\DatabaseException

Returns: **string[]** - The table names

-------
#### dbGetTableDropCommand
Get database table drop command.
```php
public function dbGetTableDropCommand(string $tableName, string $type) : string
```
Arguments:
- _$tableName_ **string** - The table name, 
- _$type_ **string** - The table or view. Onr of the self:: CREATE_TYPE_* constants.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **string**

-------
#### dbGetCreateStatement
Get create statement for the given table name.

Method returns associative array where keys are table names and values are arrays with keys:

- create - CREATE TABLE or VIEW statement
- type   - table, view ( one of the self::CREATE_TYPE_* )
- name   - table name
```php
public function dbGetCreateStatement(string $tableName, boolean $addIfNotExists) : array
```
Arguments:
- _$tableName_ **string** - The table name to get CREATE statement for, 
- _$addIfNotExists_ **boolean** - Set to true to add IF NOT EXIST to CREATE TABLE

Throws:
- Kicaj\Tools\Db\DatabaseException

Returns: **array**

-------
#### dbGetTableDefinition
Return table definitions for given database table.
```php
public function dbGetTableDefinition(string $tableName) : Kicaj\Schema\TableDefinition
```
Arguments:
- _$tableName_ **string** - The database table name

Throws:
- Kicaj\Tools\Db\DatabaseException

Returns: **[Kicaj\Schema\TableDefinition](Kicaj-Schema-TableDefinition.md)**

-------
#### dbGetCreateStatements
Get create statements for all database tables.
```php
public function dbGetCreateStatements(boolean $addIfNotExists) : array
```
Arguments:
- _$addIfNotExists_ **boolean** - Set to true to add IF NOT EXIST to CREATE TABLE

Throws:
- Kicaj\Tools\Db\DatabaseException

Returns: **array** - See SchemaGetter::dbGetCreateStatement

-------
