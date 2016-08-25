## Abstract class Kicaj\Schema\DbTable
Abstract database table class.

## Implements

- [Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)

## Methods

|                                      |                                      |                                      |                                      |                                      |
| ------------------------------------ | ------------------------------------ | ------------------------------------ | ------------------------------------ | ------------------------------------ |
|         [getName](#getname)          |         [getType](#gettype)          |      [getColumns](#getcolumns)       | [getColumnByName](#getcolumnbyname)  |      [getIndexes](#getindexes)       |
|  [getIndexByName](#getindexbyname)   |  [getConstraints](#getconstraints)   |                [](#)                 |                [](#)                 |                [](#)                 |

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
#### getName
Return table name.
```php
public function getName() : 
```

-------
#### getType
Return table type.
```php
public function getType() : 
```

-------
#### getColumns
Return table columns.

Associative array columnName =&gt; Column
```php
public function getColumns() : 
```

-------
#### getColumnByName
Return table column by its name.
```php
public function getColumnByName() : 
```

-------
#### getIndexes
Get table indexes.

Associative array indexName =&gt; Column
```php
public function getIndexes() : 
```

-------
#### getIndexByName
Return table index by its name.
```php
public function getIndexByName() : 
```

-------
#### getConstraints
Return index constraints.

Associative array constraintName =&gt; Constraint
```php
public function getConstraints() : 
```

-------
