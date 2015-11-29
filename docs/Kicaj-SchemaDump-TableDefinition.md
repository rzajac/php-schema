## Class Kicaj\SchemaDump\TableDefinition
Database table definition.

## Methods

|                                  |                                  |                                  |                                  |                                  |
| -------------------------------- | -------------------------------- | -------------------------------- | -------------------------------- | -------------------------------- |
|   [__construct](#__construct)    |          [make](#make)           |     [addColumn](#addcolumn)      |      [addIndex](#addindex)       |    [getIndexes](#getindexes)     |
|    [getColumns](#getcolumns)     |       [getName](#getname)        | [getPrimaryKey](#getprimarykey)  |              [](#)               |              [](#)               |

## Properties

|                              |                              |                              |                              |
| ---------------------------- | ---------------------------- | ---------------------------- | ---------------------------- |
|     [$columns](#columns)     |   [$tableName](#tablename)   |  [$primaryKey](#primarykey)  |     [$indexes](#indexes)     |

-------

#### $columns
Column definitions.

```php
protected \Kicaj\SchemaDump\ColumnDefinition[] $columns = array()
```

#### $tableName
The table name.

```php
protected string $tableName
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
public function __construct(string $tableName) : 
```
Arguments:
- _$tableName_ **string** - The database table name

-------
#### make
Make.
```php
public static function make(string $tableName) : static
```
Arguments:
- _$tableName_ **string** - The database table name

Returns: **static**

-------
#### addColumn
Add database column definition to the table.
```php
public function addColumn([Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md) $colDef) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$colDef_ **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

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
public function getColumns() : \Kicaj\SchemaDump\ColumnDefinition[]
```

Returns: **\Kicaj\SchemaDump\ColumnDefinition[]**

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
