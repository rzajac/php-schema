## Class Kicaj\SchemaDump\ColumnDefinition
Database column definition.

## Methods

|                                            |                                            |                                            |                                            |
| ------------------------------------------ | ------------------------------------------ | ------------------------------------------ | ------------------------------------------ |
|        [__construct](#__construct)         |               [make](#make)                |         [getPhpType](#getphptype)          |         [setPhpType](#setphptype)          |
|         [isUnsigned](#isunsigned)          |      [setIsUnsigned](#setisunsigned)       |          [isNotNull](#isnotnull)           |         [setNotNull](#setnotnull)          |
|    [isAutoincrement](#isautoincrement)     | [setIsAutoincrement](#setisautoincrement)  |         [isPartOfPk](#ispartofpk)          |      [setIsPartOfPk](#setispartofpk)       |
|            [getName](#getname)             |       [getTableName](#gettablename)        |    [getDefaultValue](#getdefaultvalue)     |    [setDefaultValue](#setdefaultvalue)     |
|        [getMinValue](#getminvalue)         |        [setMinValue](#setminvalue)         |        [getMaxValue](#getmaxvalue)         |        [setMaxValue](#setmaxvalue)         |
|       [getMinLength](#getminlength)        |       [setMinLength](#setminlength)        |       [getMaxLength](#getmaxlength)        |       [setMaxLength](#setmaxlength)        |
|      [getDriverName](#getdrivername)       |          [getDbType](#getdbtype)           |          [setDbType](#setdbtype)           |     [getValidValues](#getvalidvalues)      |
|     [setValidValues](#setvalidvalues)      |                   [](#)                    |                   [](#)                    |                   [](#)                    |

## Properties

|                                        |                                        |                                        |                                        |
| -------------------------------------- | -------------------------------------- | -------------------------------------- | -------------------------------------- |
|       [$driverName](#drivername)       |          [$phpType](#phptype)          |           [$dbType](#dbtype)           |       [$isUnsigned](#isunsigned)       |
|          [$notNull](#notnull)          |  [$isAutoincrement](#isautoincrement)  |       [$isPartOfPk](#ispartofpk)       |             [$name](#name)             |
|        [$tableName](#tablename)        |     [$defaultValue](#defaultvalue)     |         [$minValue](#minvalue)         |         [$maxValue](#maxvalue)         |
|        [$minLength](#minlength)        |        [$maxLength](#maxlength)        |      [$validValues](#validvalues)      |                 [](#)                  |

-------

#### $driverName
The driver name that initialized this column definition.

```php
protected string $driverName = ''
```

#### $phpType
The php type for the column.

```php
protected string $phpType = ''
```

#### $dbType
The type understood by the driver this column belongs to.

```php
protected string $dbType = ''
```

#### $isUnsigned
Is column set as unsigned.

```php
protected boolean $isUnsigned = false
```

#### $notNull
Is null value allowed for the column.

```php
protected boolean $notNull = false
```

#### $isAutoincrement
Is autoincrement column.

```php
protected boolean $isAutoincrement = false
```

#### $isPartOfPk
Is column part of primary key.

```php
protected boolean $isPartOfPk = false
```

#### $name
The column name.

```php
protected string $name = ''
```

#### $tableName
Name of the table column belongs to.

```php
protected string $tableName = ''
```

#### $defaultValue
The default value for the column.

```php
protected mixed $defaultValue
```

#### $minValue
The minimum value for the column.
Has meaning only for numerical columns.
```php
protected integer $minValue
```

#### $maxValue
The maximum value for the column.
Has meaning only for numerical columns.
```php
protected integer $maxValue
```

#### $minLength
Minimum column length.
Has meaning only for string values.
```php
protected integer $minLength
```

#### $maxLength
Maximum column length.
Has meaning only for string values.
```php
protected integer $maxLength
```

#### $validValues
Valid values for array types.

```php
protected array $validValues
```

-------
## Methods
#### __construct
Constructor.
```php
public function __construct(string $columnName, string $driverName, string $tableName) : 
```
Arguments:
- _$columnName_ **string** - The column name, 
- _$driverName_ **string** - The driver name that is setting up ColumnDefinition.
The one of DbConnector::DB_DRIVER_* constants., 
- _$tableName_ **string** - The table name column belongs to

-------
#### make
Make.
```php
public static function make(string $columnName, string $driverName, string $tableName) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$columnName_ **string** - The column name, 
- _$driverName_ **string** - The driver name that is setting up ColumnDefinition.
The one of DbConnector::DB_DRIVER_* constants., 
- _$tableName_ **string** - The table name column belongs to

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### getPhpType
Return PHP type assigned to this column.
```php
public function getPhpType() : string
```

Returns: **string** - One of the SchemaDump::PHP_TYPE_* constants

-------
#### setPhpType
Set PHP type for this column.
```php
public function setPhpType(string $phpType) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$phpType_ **string** - One of the SchemaDump::PHP_TYPE_* constants

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### isUnsigned
Is column set as unsigned.
```php
public function isUnsigned() : boolean
```

Returns: **boolean**

-------
#### setIsUnsigned
Set column as signed or unsigned.
```php
public function setIsUnsigned(boolean $isUnsigned) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$isUnsigned_ **boolean**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### isNotNull
Is null value allowed for the column.
```php
public function isNotNull() : boolean
```

Returns: **boolean**

-------
#### setNotNull
Set if null is allowed for the column.
```php
public function setNotNull(boolean $notNull) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$notNull_ **boolean**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### isAutoincrement
Is column set as autoincrement.
```php
public function isAutoincrement() : boolean
```

Returns: **boolean**

-------
#### setIsAutoincrement
Set column as autoincrement or not.
```php
public function setIsAutoincrement(boolean $isAutoincrement) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$isAutoincrement_ **boolean**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### isPartOfPk
Is column part of primary key for the table.
```php
public function isPartOfPk() : boolean
```

Returns: **boolean**

-------
#### setIsPartOfPk
Set column as being part of primary key for the table.
```php
public function setIsPartOfPk(boolean $isPartOfPk) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$isPartOfPk_ **boolean**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### getName
Return the column name.
```php
public function getName() : string
```

Returns: **string**

-------
#### getTableName
Return table name this column belongs to.
```php
public function getTableName() : string
```

Returns: **string**

-------
#### getDefaultValue
Return default value for the column.
```php
public function getDefaultValue() : mixed
```

Returns: **mixed** - Returns null when not set.

-------
#### setDefaultValue
Set default value for column.
```php
public function setDefaultValue(mixed $defaultValue) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$defaultValue_ **mixed**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### getMinValue
Return minimum value the column may have.
```php
public function getMinValue() : float|integer
```

Returns: **float|integer** - Returns null is not set

-------
#### setMinValue
Set minimum value the column may have.
```php
public function setMinValue(float|integer $minValue) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$minValue_ **float|integer** - Set to null to unset

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### getMaxValue
Return maximum value the column may have.
```php
public function getMaxValue() : float|integer
```

Returns: **float|integer**

-------
#### setMaxValue
Set maximum value the column may have.
```php
public function setMaxValue(float|integer $maxValue) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$maxValue_ **float|integer** - Set to null to unset

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### getMinLength
Return minimum length for column value.

This has meaning only for string types.
```php
public function getMinLength() : integer
```

Returns: **integer**

-------
#### setMinLength
Set minimum length for column value.

This has meaning only for string types.
```php
public function setMinLength(integer $minLength) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$minLength_ **integer**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### getMaxLength
Return maximum length for column value.

This has meaning only for string types.
```php
public function getMaxLength() : integer
```

Returns: **integer**

-------
#### setMaxLength
Set maximum length for column value.

This has meaning only for string types.
```php
public function setMaxLength(integer $maxLength) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$maxLength_ **integer**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### getDriverName
Return database driver name that set up ColumnDefinition.
```php
public function getDriverName() : string
```

Returns: **string**

-------
#### getDbType
Get the database type of this column.
```php
public function getDbType() : string
```

Returns: **string**

-------
#### setDbType
Set database type of this column.
```php
public function setDbType(string $dbType) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$dbType_ **string**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
#### getValidValues
Return valid values for the column.
```php
public function getValidValues() : array|null
```

Returns: **array|null** - Returns null if not applicable

-------
#### setValidValues
Set valid values for the column.
```php
public function setValidValues(array $validValues) : Kicaj\SchemaDump\ColumnDefinition
```
Arguments:
- _$validValues_ **array**

Returns: **[Kicaj\SchemaDump\ColumnDefinition](Kicaj-SchemaDump-ColumnDefinition.md)**

-------
