## Class Kicaj\Schema\Database\MySQL\Index
MySQL index.

## Extends

- Kicaj\Schema\DbIndex

## Methods

|                                  |                                  |                                  |
| -------------------------------- | -------------------------------- | -------------------------------- |
|   [__construct](#__construct)    | [parseIndexDef](#parseindexdef)  |    [isIndexDef](#isindexdef)     |

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
#### __construct
Constructor.
```php
public function __construct(string $indexDef, [Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md) $table) : 
```
Arguments:
- _$indexDef_ **string** - The index definition., 
- _$table_ **[Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)** - The table index belongs to.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### parseIndexDef
Parse index definition.
```php
protected function parseIndexDef() : 
```

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### isIndexDef
Returns true if line is definition of one of the indexes.
```php
public static function isIndexDef(string $line) : boolean
```
Arguments:
- _$line_ **string** - The line from CREATE STATEMENT.

Returns: **boolean**

-------
