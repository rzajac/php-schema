## Class Kicaj\Schema\Database\MySQL\Column
MySQL column.

## Extends

- Kicaj\Schema\DbColumn

## Methods

|                                                        |                                                        |                                                        |
| ------------------------------------------------------ | ------------------------------------------------------ | ------------------------------------------------------ |
|              [__construct](#__construct)               |              [parseColumn](#parsecolumn)               |           [parseMySQLType](#parsemysqltype)            |
|      [parseAndSetColExtra](#parseandsetcolextra)       |            [parseUnsigned](#parseunsigned)             |           [isDbNumberType](#isdbnumbertype)            |
| [setLengthsAndValidValues](#setlengthsandvalidvalues)  |          [setDefaultValue](#setdefaultvalue)           |              [setPartOfPk](#setpartofpk)               |
|            [setTypeBounds](#settypebounds)             |            [getDriverName](#getdrivername)             |                         [](#)                          |

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
#### __construct
Constructor.
```php
public function __construct(string $columnDef, integer $index, [Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md) $table) : 
```
Arguments:
- _$columnDef_ **string** - The column definition as returned by SHOW CREATE TABLE query., 
- _$index_ **integer** - The zero based index of the column in the table., 
- _$table_ **[Kicaj\Schema\Itf\TableItf](Kicaj-Schema-Itf-TableItf.md)** - The database table this column belongs to.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### parseColumn
Parse database column definition.
```php
protected function parseColumn() : 
```

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### parseMySQLType
Parse MySQL column type.
```php
protected function parseMySQLType(string $dbType) : string
```
Arguments:
- _$dbType_ **string** - The database type definition.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

Returns: **string** - The one of \Kicaj\Schema\Database\MySQL\MySQL::TYPE_* constants.

-------
#### parseAndSetColExtra
Parse extra column definitions.
```php
protected function parseAndSetColExtra(string $colDefExtra) : 
```
Arguments:
- _$colDefExtra_ **string** - The extra column definitions.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### parseUnsigned
Parse extra column definitions.
```php
protected function parseUnsigned(string $colDefExtra) : 
```
Arguments:
- _$colDefExtra_ **string** - The extra column definitions.

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### isDbNumberType
Is database type number type.
```php
protected function isDbNumberType() : boolean
```

Returns: **boolean**

-------
#### setLengthsAndValidValues
Parse type length values.
```php
protected function setLengthsAndValidValues(string $dbTypeDef) : 
```
Arguments:
- _$dbTypeDef_ **string** - The database type definition.

-------
#### setDefaultValue
Set default value for column.
```php
public function setDefaultValue(mixed $defaultValue) : 
```
Arguments:
- _$defaultValue_ **mixed** - The default value to set.

-------
#### setPartOfPk
Set column is part of primary key.
```php
public function setPartOfPk() : 
```

-------
#### setTypeBounds
Set type bounds.
```php
protected function setTypeBounds() : 
```

Throws:
- [Kicaj\Schema\SchemaException](Kicaj-Schema-SchemaException.md)

-------
#### getDriverName

```php
public function getDriverName() : 
```

-------
