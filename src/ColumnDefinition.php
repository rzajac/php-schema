<?php
/**
 * Copyright 2015 Rafal Zajac <rzajac@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */
namespace Kicaj\Schema;

/**
 * Database column definition.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class ColumnDefinition
{
    /**
     * The driver name that initialized this column definition.
     *
     * @var string
     */
    protected $driverName = '';

    /**
     * The php type for the column.
     *
     * @var string One of the Schema::PHP_TYPE_* constants
     */
    protected $phpType = '';

    /**
     * The type understood by the driver this column belongs to.
     *
     * @var string
     */
    protected $dbType = '';

    /**
     * Is column set as unsigned.
     *
     * @var bool
     */
    protected $isUnsigned = false;

    /**
     * Is null value allowed for the column.
     *
     * @var bool
     */
    protected $notNull = false;

    /**
     * Is autoincrement column.
     *
     * @var bool
     */
    protected $isAutoincrement = false;

    /**
     * Is column part of primary key.
     *
     * @var bool
     */
    protected $isPartOfPk = false;

    /**
     * The column name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Name of the table column belongs to.
     *
     * @var string
     */
    protected $tableName = '';

    /**
     * The default value for the column.
     *
     * @var mixed
     */
    protected $defaultValue;

    /**
     * The minimum value for the column.
     *
     * Has meaning only for numerical columns.
     *
     * @var int|float
     */
    protected $minValue;

    /**
     * The maximum value for the column.
     *
     * Has meaning only for numerical columns.
     *
     * @var int|float
     */
    protected $maxValue;

    /**
     * Minimum column length.
     *
     * Has meaning only for string values.
     *
     * @var int
     */
    protected $minLength;

    /**
     * Maximum column length.
     *
     * Has meaning only for string values.
     *
     * @var int
     */
    protected $maxLength;

    /**
     * Valid values for array types.
     *
     * @var array
     */
    protected $validValues;

    /**
     * Constructor.
     *
     * @param string $columnName The column name
     * @param string $driverName The driver name that is setting up ColumnDefinition.
     *                           The one of DbConnector::DB_DRIVER_* constants.
     * @param string $tableName  The table name column belongs to
     */
    public function __construct($columnName, $driverName, $tableName)
    {
        $this->name = $columnName;
        $this->driverName = $driverName;
        $this->tableName = $tableName;
    }

    /**
     * Make.
     *
     * @param string $columnName The column name
     * @param string $driverName The driver name that is setting up ColumnDefinition.
     *                           The one of DbConnector::DB_DRIVER_* constants.
     * @param string $tableName  The table name column belongs to
     *
     * @return ColumnDefinition
     */
    public static function make($columnName, $driverName, $tableName = '')
    {
        return new static($columnName, $driverName, $tableName);
    }

    /**
     * Return PHP type assigned to this column.
     *
     * @return string One of the Schema::PHP_TYPE_* constants
     */
    public function getPhpType()
    {
        return $this->phpType;
    }

    /**
     * Set PHP type for this column.
     *
     * @param string $phpType One of the Schema::PHP_TYPE_* constants
     *
     * @return ColumnDefinition
     */
    public function setPhpType($phpType)
    {
        $this->phpType = $phpType;

        return $this;
    }

    /**
     * Is column set as unsigned.
     *
     * @return bool
     */
    public function isUnsigned()
    {
        return $this->isUnsigned;
    }

    /**
     * Set column as signed or unsigned.
     *
     * @param bool $isUnsigned
     *
     * @return ColumnDefinition
     */
    public function setIsUnsigned($isUnsigned = true)
    {
        $this->isUnsigned = (bool) $isUnsigned;

        return $this;
    }

    /**
     * Is null value allowed for the column.
     *
     * @return bool
     */
    public function isNotNull()
    {
        return $this->notNull;
    }

    /**
     * Set if null is allowed for the column.
     *
     * @param bool $notNull
     *
     * @return ColumnDefinition
     */
    public function setNotNull($notNull = true)
    {
        $this->notNull = (bool) $notNull;

        return $this;
    }

    /**
     * Is column set as autoincrement.
     *
     * @return bool
     */
    public function isAutoincrement()
    {
        return $this->isAutoincrement;
    }

    /**
     * Set column as autoincrement or not.
     *
     * @param bool $isAutoincrement
     *
     * @return ColumnDefinition
     */
    public function setIsAutoincrement($isAutoincrement = true)
    {
        $this->isAutoincrement = (bool) $isAutoincrement;

        return $this;
    }

    /**
     * Is column part of primary key for the table.
     *
     * @return bool
     */
    public function isPartOfPk()
    {
        return $this->isPartOfPk;
    }

    /**
     * Set column as being part of primary key for the table.
     *
     * @param bool $isPartOfPk
     *
     * @return ColumnDefinition
     */
    public function setIsPartOfPk($isPartOfPk)
    {
        $this->isPartOfPk = (bool) $isPartOfPk;

        return $this;
    }

    /**
     * Return the column name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return table name this column belongs to.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Return default value for the column.
     *
     * @return mixed Returns null when not set.
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set default value for column.
     *
     * @param mixed $defaultValue
     *
     * @return ColumnDefinition
     */
    public function setDefaultValue($defaultValue)
    {
        if ($defaultValue === null && $this->getPhpType() !== Schema::PHP_TYPE_BOOL) {
            $this->defaultValue = null;

            return $this;
        }

        switch ($this->getPhpType()) {
            case Schema::PHP_TYPE_INT:
                $this->defaultValue = (int) $defaultValue;
                break;

            case Schema::PHP_TYPE_FLOAT:
                $this->defaultValue = (float) $defaultValue;
                break;

            case Schema::PHP_TYPE_BOOL:
                $this->defaultValue = (bool) $defaultValue;
                break;

            default:
                $this->defaultValue = $defaultValue;
        }

        return $this;
    }

    /**
     * Return minimum value the column may have.
     *
     * @return float|int Returns null is not set
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * Set minimum value the column may have.
     *
     * @param float|int $minValue Set to null to unset
     *
     * @return ColumnDefinition
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;

        return $this;
    }

    /**
     * Return maximum value the column may have.
     *
     * @return float|int
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * Set maximum value the column may have.
     *
     * @param float|int $maxValue Set to null to unset
     *
     * @return ColumnDefinition
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }

    /**
     * Return minimum length for column value.
     *
     * This has meaning only for string types.
     *
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * Set minimum length for column value.
     *
     * This has meaning only for string types.
     *
     * @param int $minLength
     *
     * @return ColumnDefinition
     */
    public function setMinLength($minLength = 0)
    {
        $this->minLength = (int) $minLength;

        return $this;
    }

    /**
     * Return maximum length for column value.
     *
     * This has meaning only for string types.
     *
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * Set maximum length for column value.
     *
     * This has meaning only for string types.
     *
     * @param int $maxLength
     *
     * @return ColumnDefinition
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    /**
     * Return database driver name that set up ColumnDefinition.
     *
     * @return string
     */
    public function getDriverName()
    {
        return $this->driverName;
    }

    /**
     * Get the database type of this column.
     *
     * @return string
     */
    public function getDbType()
    {
        return $this->dbType;
    }

    /**
     * Set database type of this column.
     *
     * @param string $dbType
     *
     * @return ColumnDefinition
     */
    public function setDbType($dbType)
    {
        $this->dbType = $dbType;

        return $this;
    }

    /**
     * Return valid values for the column.
     *
     * @return array|null Returns null if not applicable
     */
    public function getValidValues()
    {
        return $this->validValues;
    }

    /**
     * Set valid values for the column.
     *
     * @param array $validValues
     *
     * @return ColumnDefinition
     */
    public function setValidValues(array $validValues)
    {
        $this->validValues = $validValues;

        return $this;
    }
}
