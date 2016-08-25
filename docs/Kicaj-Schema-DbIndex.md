## Abstract class Kicaj\Schema\DbIndex
Abstract database index class.

## Implements

- [Kicaj\Schema\Itf\IndexItf](Kicaj-Schema-Itf-IndexItf.md)

## Methods

|                                    |                                    |                                    |                                    |                                    |
| ---------------------------------- | ---------------------------------- | ---------------------------------- | ---------------------------------- | ---------------------------------- |
|        [getName](#getname)         |        [getType](#gettype)         |       [getTable](#gettable)        |     [getColumns](#getcolumns)      | [getColumnNames](#getcolumnnames)  |

## Properties

|                                |                                |                                |                                |                                |                                |
| ------------------------------ | ------------------------------ | ------------------------------ | ------------------------------ | ------------------------------ | ------------------------------ |
|     [$indexDef](#indexdef)     |        [$table](#table)        |         [$name](#name)         |         [$type](#type)         |  [$columnNames](#columnnames)  |      [$columns](#columns)      |

-------

#### $indexDef
Index definition.

```php
protected string $indexDef
```

#### $table
The table index belongs to.

```php
protected \Kicaj\Schema\Itf\TableItf $table
```

#### $name
The index name.

```php
protected string $name
```

#### $type
The index type.

```php
protected string $type
```

#### $columnNames
The list of columns this index is composed of.
The order of the array does matter.
```php
protected string[] $columnNames = array()
```

#### $columns
The columns the index is composed of.
The order in the array does matter.

The array is associative:

columnName =&gt; ColumnItf
```php
protected \Kicaj\Schema\Itf\ColumnItf[] $columns = array()
```

-------
## Methods
#### getName
Get index name.
```php
public function getName() : 
```

-------
#### getType
Get index type.
```php
public function getType() : 
```

-------
#### getTable
Return table this index belongs to.
```php
public function getTable() : 
```

-------
#### getColumns
Return columns this index is composed of.

The order of columns matter.

The array is associative:

columnName =&gt; ColumnItf
```php
public function getColumns() : 
```

-------
#### getColumnNames
Return names of columns this index is composed of.

The order in the array does matter.
```php
public function getColumnNames() : string[]
```

Returns: **string[]**

-------
