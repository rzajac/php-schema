## Final class Kicaj\Schema\Database\SchemaFactory
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
public static function factory(array $dbConfig, boolean $connect) : Kicaj\Schema\SchemaGetter
```
Arguments:
- _$dbConfig_ **array** - The database configuration, 
- _$connect_ **boolean** - Set to true to also connect to the database

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md), 
- Kicaj\Tools\Db\DatabaseException

Returns: **[Kicaj\Schema\SchemaGetter](Kicaj-Schema-SchemaGetter.md)**

-------
#### _resetInstances
Reset instances cache.

This is used only during unit tests.
```php
public static function _resetInstances() : 
```

-------
