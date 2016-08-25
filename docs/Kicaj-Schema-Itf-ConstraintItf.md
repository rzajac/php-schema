## Interface Kicaj\Schema\Itf\ConstraintItf
Database table index interface.

## Methods

|                                              |                                              |                                              |                                              |
| -------------------------------------------- | -------------------------------------------- | -------------------------------------------- | -------------------------------------------- |
|             [getName](#getname)              |            [getTable](#gettable)             |            [getIndex](#getindex)             | [getForeignTableName](#getforeigntablename)  |
| [getForeignIndexName](#getforeignindexname)  |                    [](#)                     |                    [](#)                     |                    [](#)                     |

-------
## Methods
#### getName
Return constraint name.
```php
public function getName() : string
```

Returns: **string**

-------
#### getTable
Return table this index constraint belongs to.
```php
public function getTable() : Kicaj\Schema\Itf\TableItf
```

Returns: **[Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)**

-------
#### getIndex
Return the index the constraint is on.
```php
public function getIndex() : Kicaj\Schema\Itf\IndexItf
```

Returns: **[Kicaj\Schema\Itf\IndexItf](Kicaj-Schema-Itf-IndexItf.md)**

-------
#### getForeignTableName
Return the foreign table name.
```php
public function getForeignTableName() : string
```

Returns: **string**

-------
#### getForeignIndexName
Return the foreign index name.
```php
public function getForeignIndexName() : string
```

Returns: **string**

-------
