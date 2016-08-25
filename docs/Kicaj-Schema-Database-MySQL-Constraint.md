## Class Kicaj\Schema\Database\MySQL\Constraint
MySQL index constraint.

## Implements

- [Kicaj\Schema\Itf\ConstraintItf](Kicaj-Schema-Itf-ConstraintItf.md)

## Methods

|                                              |                                              |                                              |                                              |
| -------------------------------------------- | -------------------------------------------- | -------------------------------------------- | -------------------------------------------- |
|         [__construct](#__construct)          |     [parseConstraint](#parseconstraint)      |     [isConstraintDef](#isconstraintdef)      |             [getName](#getname)              |
|   [getForeignKeyName](#getforeignkeyname)    |            [getIndex](#getindex)             | [getForeignTableName](#getforeigntablename)  | [getForeignIndexName](#getforeignindexname)  |
|            [getTable](#gettable)             |                    [](#)                     |                    [](#)                     |                    [](#)                     |

## Properties

|                              |                              |                              |                              |                              |                              |
| ---------------------------- | ---------------------------- | ---------------------------- | ---------------------------- | ---------------------------- | ---------------------------- |
|    [$constDef](#constdef)    |       [$table](#table)       |        [$name](#name)        |    [$fKeyName](#fkeyname)    |  [$fTableName](#ftablename)  |  [$fIndexName](#findexname)  |

-------

#### $constDef
The index constraint definition.

```php
protected string $constDef
```

#### $table
The table the index constraint belongs to.

```php
protected \Kicaj\Schema\Database\MySQL\Table $table
```

#### $name
The index constraint name.

```php
protected string $name
```

#### $fKeyName
The index name the constraint is on.

```php
protected string $fKeyName
```

#### $fTableName
The foreign table name.

```php
protected string $fTableName
```

#### $fIndexName
The foreign index name.

```php
protected string $fIndexName
```

-------
## Methods
#### __construct
Constructor.
```php
public function __construct(string $constDef, [Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md) $table) : 
```
Arguments:
- _$constDef_ **string** - The index constraint definition., 
- _$table_ **[Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)** - The table index constraint belongs to.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### parseConstraint
Parse index constraint.
```php
protected function parseConstraint() : 
```

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### isConstraintDef
Returns true if line is definition of one of the index constraints.
```php
public static function isConstraintDef(string $line) : boolean
```
Arguments:
- _$line_ **string** - The line from CREATE STATEMENT.

Returns: **boolean**

-------
#### getName
Return constraint name.
```php
public function getName() : 
```

-------
#### getForeignKeyName
Return name of the foreign key.
```php
public function getForeignKeyName() : string
```

Returns: **string**

-------
#### getIndex
Return the index the constraint is on.
```php
public function getIndex() : 
```

-------
#### getForeignTableName
Return the foreign table name.
```php
public function getForeignTableName() : 
```

-------
#### getForeignIndexName
Return the foreign index name.
```php
public function getForeignIndexName() : 
```

-------
#### getTable
Return table this index constraint belongs to.
```php
public function getTable() : 
```

-------
