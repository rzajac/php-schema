## Final class Kicaj\SchemaDump\Database\SchemaDumpFactory
Helper class for getting database driver.

## Methods

|                                      |                                      |
| ------------------------------------ | ------------------------------------ |
|         [factory](#factory)          | [_resetInstances](#_resetinstances)  |

## Properties

|                            |
| -------------------------- |
|  [$instances](#instances)  |

-------

-------
## Methods
#### factory
Database factory.

It returns the same instance for the same config.
```php
public static function factory(array $dbConfig, boolean $connect) : Kicaj\SchemaDump\SchemaGetter
```
Arguments:
- _$dbConfig_ **array** - The database configuration, 
- _$connect_ **boolean** - Set to true to also connect to the database

Throws:
- [Kicaj\SchemaDump\SchemaException](Kicaj-SchemaDump-SchemaException.md)

Returns: **[Kicaj\SchemaDump\SchemaGetter](Kicaj-SchemaDump-SchemaGetter.md)**

-------
#### _resetInstances
Reset instances cache.

This is used only during unit tests.1391j8ri9X?o
```php
public static function _resetInstances() : 
```

-------
