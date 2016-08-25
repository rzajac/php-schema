## Abstract class Kicaj\Schema\DbColumn
Abstract database column class.

## Implements

- [Kicaj\Schema\Itf\ColumnItf](Kicaj-Schema-Itf-ColumnItf.md)

## Methods

|                                      |                                      |                                      |                                      |                                      |
| ------------------------------------ | ------------------------------------ | ------------------------------------ | ------------------------------------ | ------------------------------------ |
|         [getName](#getname)          |     [getPosition](#getposition)      |        [getTable](#gettable)         |      [isUnsigned](#isunsigned)       |   [isNullAllowed](#isnullallowed)    |
| [isAutoincrement](#isautoincrement)  |      [isPartOfPk](#ispartofpk)       | [getDefaultValue](#getdefaultvalue)  |      [getPhpType](#getphptype)       |     [getMinValue](#getminvalue)      |
|     [getMaxValue](#getmaxvalue)      |    [getMinLength](#getminlength)     |    [getMaxLength](#getmaxlength)     |       [getDbType](#getdbtype)        |  [getValidValues](#getvalidvalues)   |

## Properties

|                                        |                                        |                                        |                                        |
| -------------------------------------- | -------------------------------------- | -------------------------------------- | -------------------------------------- |
|        [$columnDef](#columndef)        |             [$name](#name)             |            [$index](#index)            |            [$table](#table)            |
|           [$dbType](#dbtype)           |          [$typeMap](#typemap)          |       [$isUnsigned](#isunsigned)       |  [$isAutoincrement](#isautoincrement)  |
|       [$isPartOfPk](#ispartofpk)       |    [$isNullAllowed](#isnullallowed)    |     [$defaultValue](#defaultvalue)     |        [$minLength](#minlength)        |
|        [$maxLength](#maxlength)        |         [$minValue](#minvalue)         |         [$maxValue](#maxvalue)         |      [$validValues](#validvalues)      |

-------

#### $columnDef
The column definition as returned by SHOW CREATE TABLE query.

```php
protected string $columnDef
```

#### $name
The column name.

```php
protected string $name
```

#### $index
Zero based index of the column in the table.

```php
protected integer $index
```

#### $table
The database table this column belongs to.

```php
protected \Kicaj\Schema\Itf\TableItf $table
```

#### $dbType
The column database type.

```php
protected string $dbType
```

#### $typeMap
The map between MySQL types and PHP types.

```php
protected array $typeMap
```

#### $isUnsigned
Is column marked as unsigned.

```php
protected boolean $isUnsigned
```

#### $isAutoincrement
Is column marked as autoincrement.

```php
protected boolean $isAutoincrement
```

#### $isPartOfPk
Is column part of the primary key.

```php
protected boolean $isPartOfPk = false
```

#### $isNullAllowed
Are NULLs allowed for the column value.

```php
protected boolean $isNullAllowed = true
```

#### $defaultValue
Default column value.

```php
protected mixed $defaultValue
```

#### $minLength
The minimum length.
This has meaning only for string types.
```php
protected integer $minLength
```

#### $maxLength
The maximum length.
This has meaning only for string types.
```php
protected integer $maxLength
```

#### $minValue
The min value for the column.
This has meaning only for int, float and date types.
```php
protected integer $minValue
```

#### $maxValue
The max value for the column.
This has meaning only for int, float and date types.
```php
protected integer $maxValue
```

#### $validValues
Valid values for the column.
This has the meaning only for set and enums.
```php
protected array $validValues
```

-------
## Methods
#### getName
Return column name.
```php
public function getName() : 
```

-------
#### getPosition
Returns 0 based position of column in the table.
```php
public function getPosition() : 
```

-------
#### getTable
Return table this column belongs to.
```php
public function getTable() : 
```

-------
#### isUnsigned
Is column set as unsigned.
```php
public function isUnsigned() : 
```

-------
#### isNullAllowed
Is null value allowed for the column.
```php
public function isNullAllowed() : 
```

-------
#### isAutoincrement
Is column set as autoincrement.
```php
public function isAutoincrement() : 
```

-------
#### isPartOfPk
Is column part of primary key for the table.
```php
public function isPartOfPk() : 
```

-------
#### getDefaultValue
Return default value for the column.
```php
public function getDefaultValue() : 
```

-------
#### getPhpType
Return PHP type assigned to this column.
```php
public function getPhpType() : 
```

-------
#### getMinValue
Return minimum value the column may have.

This has meaning only for int, float and date types.
```php
public function getMinValue() : 
```

-------
#### getMaxValue
Return maximum value the column may have.

This has meaning only for int, float and date types.
```php
public function getMaxValue() : 
```

-------
#### getMinLength
Return minimum length for column value.

This has meaning only for string types.
```php
public function getMinLength() : 
```

-------
#### getMaxLength
Return maximum length for column value.

This has meaning only for string types.
```php
public function getMaxLength() : 
```

-------
#### getDbType
Get the database specific type of this column.
```php
public function getDbType() : 
```

-------
#### getValidValues
Return valid values for the column.

This has meaning only for sets and enums.
```php
public function getValidValues() : 
```

-------
