## Interface Kicaj\Schema\Itf\TableItf
Database table interface.

## Constants

```php
const TYPE_TABLE = 'table';
const TYPE_VIEW = 'view';
const TYPE_NONE = '';
```

## Methods

|                                            |                                            |                                            |                                            |
| ------------------------------------------ | ------------------------------------------ | ------------------------------------------ | ------------------------------------------ |
|            [getName](#getname)             |            [getType](#gettype)             |         [getColumns](#getcolumns)          |    [getColumnByName](#getcolumnbyname)     |
|         [getIndexes](#getindexes)          |     [getIndexByName](#getindexbyname)      |     [getConstraints](#getconstraints)      |      [getPrimaryKey](#getprimarykey)       |
|   [getDropStatement](#getdropstatement)    | [getCreateStatement](#getcreatestatement)  |                   [](#)                    |                   [](#)                    |

-------
## Methods
#### getName
Return table name.
```php
public function getName() : string
```

Returns: **string**

-------
#### getType
Return table type.
```php
public function getType() : string
```

Returns: **string** - The one of the TableItf::TYPE_* values.

-------
#### getColumns
Return table columns.

Associative array columnName =&gt; Column
```php
public function getColumns() : \Kicaj\Schema\Itf\ColumnItf[]
```

Returns: **\Kicaj\Schema\Itf\ColumnItf[]**

-------
#### getColumnByName
Return table column by its name.
```php
public function getColumnByName(string $columnName) : Kicaj\Schema\Itf\ColumnItf
```
Arguments:
- _$columnName_ **string** - The column name.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **[Kicaj\Schema\Itf\ColumnItf](Kicaj-Schema-Itf-ColumnItf.md)**

-------
#### getIndexes
Get table indexes.

Associative array indexName =&gt; Column
```php
public function getIndexes() : \Kicaj\Schema\Itf\IndexItf[]
```

Returns: **\Kicaj\Schema\Itf\IndexItf[]**

-------
#### getIndexByName
Return table index by its name.
```php
public function getIndexByName(string $indexName) : Kicaj\Schema\Itf\ColumnItf
```
Arguments:
- _$indexName_ **string** - The index name.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **[Kicaj\Schema\Itf\ColumnItf](Kicaj-Schema-Itf-ColumnItf.md)**

-------
#### getConstraints
Return index constraints.

Associative array constraintName =&gt; Constraint
```php
public function getConstraints() : \Kicaj\Schema\Itf\ConstraintItf[]
```

Returns: **\Kicaj\Schema\Itf\ConstraintItf[]**

-------
#### getPrimaryKey
Get primary key index.
```php
public function getPrimaryKey() : Kicaj\Schema\Itf\IndexItf|null
```

Returns: **Kicaj\Schema\Itf\IndexItf|null**

-------
#### getDropStatement
Get drop statement for the table.
```php
public function getDropStatement() : string
```

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **string**

-------
#### getCreateStatement
Get create statement for the table.
```php
public function getCreateStatement(boolean $addIfNotExists) : string
```
Arguments:
- _$addIfNotExists_ **boolean** - Set to true to add if not exists condition.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **string**

-------
