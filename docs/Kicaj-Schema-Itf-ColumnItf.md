## Interface Kicaj\Schema\Itf\ColumnItf
Database table column interface.

## Constants

```php
const PHP_TYPE_INT = 'int';
const PHP_TYPE_STRING = 'string';
const PHP_TYPE_FLOAT = 'float';
const PHP_TYPE_BOOL = 'bool';
const PHP_TYPE_BINARY = 'binary';
const PHP_TYPE_ARRAY = 'array';
const PHP_TYPE_DATE = 'date';
const PHP_TYPE_DATETIME = 'datetime';
const PHP_TYPE_TIMESTAMP = 'timestamp';
const PHP_TYPE_TIME = 'time';
const PHP_TYPE_YEAR = 'year';
```

## Methods

|                                      |                                      |                                      |                                      |                                      |
| ------------------------------------ | ------------------------------------ | ------------------------------------ | ------------------------------------ | ------------------------------------ |
|         [getName](#getname)          |        [getTable](#gettable)         |     [getPosition](#getposition)      |      [isUnsigned](#isunsigned)       |   [isNullAllowed](#isnullallowed)    |
| [isAutoincrement](#isautoincrement)  |      [isPartOfPk](#ispartofpk)       | [getDefaultValue](#getdefaultvalue)  |      [getPhpType](#getphptype)       |     [getMinValue](#getminvalue)      |
|     [getMaxValue](#getmaxvalue)      |    [getMinLength](#getminlength)     |    [getMaxLength](#getmaxlength)     |   [getDriverName](#getdrivername)    |       [getDbType](#getdbtype)        |
|  [getValidValues](#getvalidvalues)   |                [](#)                 |                [](#)                 |                [](#)                 |                [](#)                 |

-------
## Methods
#### getName
Return column name.
```php
public function getName() : string
```

Returns: **string**

-------
#### getTable
Return table this column belongs to.
```php
public function getTable() : Kicaj\Schema\Itf\TableItf
```

Returns: **[Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)**

-------
#### getPosition
Returns 0 based position of column in the table.
```php
public function getPosition() : integer
```

Returns: **integer**

-------
#### isUnsigned
Is column set as unsigned.
```php
public function isUnsigned() : boolean
```

Returns: **boolean**

-------
#### isNullAllowed
Is null value allowed for the column.
```php
public function isNullAllowed() : boolean
```

Returns: **boolean**

-------
#### isAutoincrement
Is column set as autoincrement.
```php
public function isAutoincrement() : boolean
```

Returns: **boolean**

-------
#### isPartOfPk
Is column part of primary key for the table.
```php
public function isPartOfPk() : boolean
```

Returns: **boolean**

-------
#### getDefaultValue
Return default value for the column.
```php
public function getDefaultValue() : mixed
```

Returns: **mixed** - Returns null when not set.

-------
#### getPhpType
Return PHP type assigned to this column.
```php
public function getPhpType() : string
```

Returns: **string** - One of the ColumnItf::PHP_TYPE_* constants.

-------
#### getMinValue
Return minimum value the column may have.

This has meaning only for int, float and date types.
```php
public function getMinValue() : float|integer|string
```

Returns: **float|integer|string** - Returns null if not known.

-------
#### getMaxValue
Return maximum value the column may have.

This has meaning only for int, float and date types.
```php
public function getMaxValue() : float|integer|string
```

Returns: **float|integer|string** - Returns null if not known.

-------
#### getMinLength
Return minimum length for column value.

This has meaning only for string types.
```php
public function getMinLength() : integer
```

Returns: **integer** - Returns null if not known.

-------
#### getMaxLength
Return maximum length for column value.

This has meaning only for string types.
```php
public function getMaxLength() : integer
```

Returns: **integer** - Returns null if not known.

-------
#### getDriverName
Return database driver name.
```php
public function getDriverName() : string
```

Returns: **string** - The one of \Kicaj\DbKit\DbConnector::DB_DRIVER_* constants.

-------
#### getDbType
Get the database specific type of this column.
```php
public function getDbType() : string
```

Returns: **string**

-------
#### getValidValues
Return valid values for the column.

This has meaning only for sets and enums.
```php
public function getValidValues() : array|null
```

Returns: **array|null** - Returns null if not applicable.

-------
