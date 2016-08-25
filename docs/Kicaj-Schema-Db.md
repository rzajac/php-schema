## Final class Kicaj\Schema\Db
Class representing database.

## Methods

|                                                |                                                |                                                |                                                |
| ---------------------------------------------- | ---------------------------------------------- | ---------------------------------------------- | ---------------------------------------------- |
|              [factory](#factory)               |          [__construct](#__construct)           |      [dbGetTableNames](#dbgettablenames)       |       [dbGetViewNames](#dbgetviewnames)        |
| [dbGetTableDefinition](#dbgettabledefinition)  |      [_resetInstances](#_resetinstances)       |                     [](#)                      |                     [](#)                      |

## Properties

|                            |                            |
| -------------------------- | -------------------------- |
|  [$instances](#instances)  |   [$dbDriver](#dbdriver)   |

-------

#### $dbDriver
Database driver.

```php
protected \Kicaj\Schema\Itf\DatabaseItf $dbDriver
```

-------
## Methods
#### factory
Database factory.

It returns the same instance for the same config.
```php
public static function factory(array $dbConfig) : Kicaj\Schema\Db
```
Arguments:
- _$dbConfig_ **array** - The database configuration.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **[Kicaj\Schema\Db](Kicaj-Schema-Db.md)**

-------
#### __construct
Constructor.

Use factory to create the object.
```php
private function __construct([Kicaj\Schema\Itf\DatabaseItf](Kicaj-Schema-Itf-DatabaseItf.md) $driver) : 
```
Arguments:
- _$driver_ **[Kicaj\Schema\Itf\DatabaseItf](Kicaj-Schema-Itf-DatabaseItf.md)** - The database driver.

-------
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

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **[Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)**

-------
#### _resetInstances
Reset instances cache.

This is used only during unit tests.
```php
public static function _resetInstances() : 
```

-------
