## Class Kicaj\Schema\TableDefinition
Database table definition.

## Methods

|                                  |                                  |                                  |                                  |                                  |
| -------------------------------- | -------------------------------- | -------------------------------- | -------------------------------- | -------------------------------- |
|   [__construct](#__construct)    |       [getType](#gettype)        |          [make](#make)           |     [addColumn](#addcolumn)      |      [addIndex](#addindex)       |
|    [getIndexes](#getindexes)     |    [getColumns](#getcolumns)     |       [getName](#getname)        | [getPrimaryKey](#getprimarykey)  |              [](#)               |

## Properties

|                              |                              |                              |                              |                              |
| ---------------------------- | ---------------------------- | ---------------------------- | ---------------------------- | ---------------------------- |
|     [$columns](#columns)     |   [$tableName](#tablename)   |        [$type](#type)        |  [$primaryKey](#primarykey)  |     [$indexes](#indexes)     |

-------

#### $columns
Column definitions.

```php
protected \Kicaj\Schema\ColumnDefinition[] $columns = array()
```

#### $tableName
The table name.

```php
protected string $tableName
```

#### $type


```php
protected  $type
```

#### $primaryKey
Primary key column names.
[indexName, indexType, [columnNames, ...]]
```php
protected array $primaryKey = array()
```

#### $indexes
Other table indexes.
Array of arrays:

[indexName, indexType, [columnNames, ...]]
```php
protected array $indexes = array()
```

-------
## Methods
#### __construct
TableDefinition constructor.
```php
public function __construct(string $tableName, string $type) : 
```
Arguments:
- _$tableName_ **string** - The database table name., 
- _$type_ **string** - The table or view. One of the SchemaGetter::CREATE_TYPE_* constants.

-------
#### getType
Return table type.
```php
public function getType() : string
```

Returns: **string** - The one of the SchemaGetter::CREATE_TYPE_* values.

-------
#### make
Make.
```php
public static function make(string $tableName, string $type) : static
```
Arguments:
- _$tableName_ **string** - The database table name, 
- _$type_ **string** - The table or view. One of the SchemaGetter::CREATE_TYPE_* constants.

Returns: **static**

-------
#### addColumn
Add database column definition to the table.
```php
public function addColumn([Kicaj\Schema\ColumnDefinition](Kicaj-Schema-ColumnDefinition.md) $colDef) : Kicaj\Schema\TableDefinition
```
Arguments:
- _$colDef_ **[Kicaj\Schema\ColumnDefinition](Kicaj-Schema-ColumnDefinition.md)**

Returns: **[Kicaj\Schema\TableDefinition](Kicaj-Schema-TableDefinition.md)**

-------
#### addIndex
Add index definition.

The format must be:

[indexName, indexType, [$columnName,...]]
```php
public function addIndex(array $indexDefinition) : 
```
Arguments:
- _$indexDefinition_ **array**

-------
#### getIndexes
Get table indexes.
```php
public function getIndexes() : array
```

Returns: **array**

-------
#### getColumns
Return column definitions.
```php
public function getColumns() : \Kicaj\Schema\ColumnDefinition[]
```

Returns: **\Kicaj\Schema\ColumnDefinition[]**

-------
#### getName
Return table name.
```php
public function getName() : string
```

Returns: **string**

-------
#### getPrimaryKey
Get primary key column names.

[indexName, indexType, [columnNames, ...]]
```php
public function getPrimaryKey() : array
```

Returns: **array**

-------
