## Class Kicaj\Schema\Database\MySQL\Table
MySQL Table.

## Extends

- Kicaj\Schema\DbTable

## Methods

|                                                      |                                                      |                                                      |
| ---------------------------------------------------- | ---------------------------------------------------- | ---------------------------------------------------- |
|             [__construct](#__construct)              |            [parseTableCS](#parsetablecs)             |               [addColumn](#addcolumn)                |
|                [addIndex](#addindex)                 |           [addConstraint](#addconstraint)            |        [getDropStatement](#getdropstatement)         |
|      [fixCreateStatement](#fixcreatestatement)       |  [fixViewCreateStatement](#fixviewcreatestatement)   | [fixTableCreateStatement](#fixtablecreatestatement)  |
|           [getPrimaryKey](#getprimarykey)            |      [getCreateStatement](#getcreatestatement)       |                        [](#)                         |

## Properties

|                                |                                |                                |                                |                                |                                |                                |
| ------------------------------ | ------------------------------ | ------------------------------ | ------------------------------ | ------------------------------ | ------------------------------ | ------------------------------ |
|      [$tableCS](#tablecs)      |           [$db](#db)           |         [$name](#name)         |         [$type](#type)         |      [$columns](#columns)      |      [$indexes](#indexes)      |  [$constraints](#constraints)  |

-------

#### $tableCS
Create statement.

```php
protected string $tableCS
```

#### $db
The database this table belongs to.

```php
protected \Kicaj\Schema\Itf\DatabaseItf $db
```

#### $name
Table name.

```php
protected string $name
```

#### $type
Table type.
One of the DatabaseItf::CREATE_TYPE_* constants.
```php
protected string $type
```

#### $columns
Table columns.
Associative array columnName =&gt; Column
```php
protected \Kicaj\Schema\Itf\ColumnItf[] $columns = array()
```

#### $indexes
Table indexes.
Associative array indexName =&gt; Index
```php
protected \Kicaj\Schema\Itf\IndexItf[] $indexes = array()
```

#### $constraints
Table index constraints.
Associative array constraintName =&gt; Constraint
```php
protected \Kicaj\Schema\Itf\ConstraintItf[] $constraints = array()
```

-------
## Methods
#### __construct
TableDef constructor.
```php
public function __construct(string $tableCS, [Kicaj\Schema\Itf\DatabaseItf](Kicaj-Schema-Itf-DatabaseItf.md) $db) : 
```
Arguments:
- _$tableCS_ **string** - The table create statement., 
- _$db_ **[Kicaj\Schema\Itf\DatabaseItf](Kicaj-Schema-Itf-DatabaseItf.md)** - The database interface.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### parseTableCS
Parse table create statement.
```php
protected function parseTableCS() : 
```

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### addColumn
Add database column definition.

The addition order is significant.
```php
public function addColumn([Kicaj\Schema\Itf\ColumnItf](Kicaj-Schema-Itf-ColumnItf.md) $column) : Kicaj\Schema\Itf\TableItf
```
Arguments:
- _$column_ **[Kicaj\Schema\Itf\ColumnItf](Kicaj-Schema-Itf-ColumnItf.md)**

Returns: **[Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)**

-------
#### addIndex
Add index definition.
```php
public function addIndex([Kicaj\Schema\Itf\IndexItf](Kicaj-Schema-Itf-IndexItf.md) $index) : 
```
Arguments:
- _$index_ **[Kicaj\Schema\Itf\IndexItf](Kicaj-Schema-Itf-IndexItf.md)**

-------
#### addConstraint
Add index constraint.
```php
public function addConstraint([Kicaj\Schema\Itf\ConstraintItf](Kicaj-Schema-Itf-ConstraintItf.md) $constraint) : 
```
Arguments:
- _$constraint_ **[Kicaj\Schema\Itf\ConstraintItf](Kicaj-Schema-Itf-ConstraintItf.md)**

-------
#### getDropStatement

```php
public function getDropStatement() : 
```

-------
#### fixCreateStatement
Fix and rewrite CREATE statements if needed.
```php
protected function fixCreateStatement(boolean $addIfNotExists) : string
```
Arguments:
- _$addIfNotExists_ **boolean** - Set to true to add if not exists condition.

Returns: **string** - The table create statement.

-------
#### fixViewCreateStatement
Fix and rewrite CREATE statements if needed.
```php
protected function fixViewCreateStatement(boolean $addIfNotExists) : string
```
Arguments:
- _$addIfNotExists_ **boolean** - Set to true to add if not exists condition.

Returns: **string** - The table create statement.

-------
#### fixTableCreateStatement
Fix and rewrite table CREATE statement if needed.
```php
protected function fixTableCreateStatement(boolean $addIfNotExists) : string
```
Arguments:
- _$addIfNotExists_ **boolean** - Set to true to add if not exists condition.

Returns: **string** - The table create statement.

-------
#### getPrimaryKey

```php
public function getPrimaryKey() : 
```

-------
#### getCreateStatement

```php
public function getCreateStatement() : 
```

-------
