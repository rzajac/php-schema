## Interface Kicaj\Schema\Itf\IndexItf
Database table index interface.

## Constants

```php
const PRIMARY = 'PRIMARY';
const UNIQUE = 'UNIQUE';
const KEY = 'KEY';
```

## Methods

|                            |                            |                            |                            |
| -------------------------- | -------------------------- | -------------------------- | -------------------------- |
|    [getName](#getname)     |    [getType](#gettype)     | [getColumns](#getcolumns)  |   [getTable](#gettable)    |

-------
## Methods
#### getName
Get index name.
```php
public function getName() : string
```

Returns: **string**

-------
#### getType
Get index type.
```php
public function getType() : string
```

Returns: **string** - The one of IndexItf::INDEX_* constants.

-------
#### getColumns
Return columns this index is composed of.

The order of columns matter.

The array is associative:

columnName =&gt; ColumnItf
```php
public function getColumns() : \Kicaj\Schema\Itf\ColumnItf[]
```

Returns: **\Kicaj\Schema\Itf\ColumnItf[]**

-------
#### getTable
Return table this index belongs to.
```php
public function getTable() : Kicaj\Schema\Itf\TableItf
```

Returns: **[Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)**

-------
