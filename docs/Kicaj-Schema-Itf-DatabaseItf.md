## Interface Kicaj\Schema\Itf\DatabaseItf
Interface for getting database schema information.

## Methods

|                                                |                                                |                                                |
| ---------------------------------------------- | ---------------------------------------------- | ---------------------------------------------- |
|      [dbGetTableNames](#dbgettablenames)       |       [dbGetViewNames](#dbgetviewnames)        | [dbGetTableDefinition](#dbgettabledefinition)  |

-------
## Methods
#### dbGetTableNames
Get database table names.
```php
public function dbGetTableNames() : string[]
```

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **string[]** - The table names.

-------
#### dbGetViewNames
Get database view names.
```php
public function dbGetViewNames() : string[]
```

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **string[]** - The view names.

-------
#### dbGetTableDefinition
Return table definition for given database table.
```php
public function dbGetTableDefinition(string $tableName) : Kicaj\Schema\Itf\TableItf
```
Arguments:
- _$tableName_ **string** - The database table name.

Returns: **[Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)**

-------
