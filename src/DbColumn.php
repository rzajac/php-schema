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

use Kicaj\Schema\Itf\ColumnItf;
use Kicaj\Schema\Itf\TableItf;

/**
 * Abstract database column class.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
abstract class DbColumn implements ColumnItf
{
    /**
     * The column definition as returned by SHOW CREATE TABLE query.
     *
     * @var string
     */
    protected $columnDef;

    /**
     * The column name.
     *
     * @var string
     */
    protected $name;

    /**
     * Zero based index of the column in the table.
     *
     * @var int
     */
    protected $index;

    /**
     * The database table this column belongs to.
     *
     * @var TableItf
     */
    protected $table;

    /**
     * The column database type.
     *
     * @var string
     */
    protected $dbType;

    /**
     * The map between MySQL types and PHP types.
     *
     * @var array
     */
    protected $typeMap;

    /**
     * Is column marked as unsigned.
     *
     * @var bool
     */
    protected $isUnsigned;

    /**
     * Is column marked as autoincrement.
     *
     * @var bool
     */
    protected $isAutoincrement;

    /**
     * Is column part of the primary key.
     *
     * @var bool
     */
    protected $isPartOfPk = false;

    /**
     * Are NULLs allowed for the column value.
     *
     * @var bool
     */
    protected $isNullAllowed = true;

    /**
     * Default column value.
     *
     * @var mixed
     */
    protected $defaultValue;

    /**
     * The minimum length.
     *
     * This has meaning only for string types.
     *
     * @var int
     */
    protected $minLength;

    /**
     * The maximum length.
     *
     * This has meaning only for string types.
     *
     * @var int
     */
    protected $maxLength;

    /**
     * The min value for the column.
     *
     * This has meaning only for int, float and date types.
     *
     * @var int|float|string
     */
    protected $minValue;

    /**
     * The max value for the column.
     *
     * This has meaning only for int, float and date types.
     *
     * @var int|float|string
     */
    protected $maxValue;

    /**
     * Valid values for the column.
     *
     * This has the meaning only for set and enums.
     *
     * @var array
     */
    protected $validValues;

    public function getName()
    {
        return $this->name;
    }

    public function getPosition()
    {
        return $this->index;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function isUnsigned()
    {
        return $this->isUnsigned;
    }

    public function isNullAllowed()
    {
        return $this->isNullAllowed;
    }

    public function isAutoincrement()
    {
        return $this->isAutoincrement;
    }

    public function isPartOfPk()
    {
        return $this->isPartOfPk;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function getPhpType()
    {
        return $this->typeMap[$this->dbType];
    }

    public function getMinValue()
    {
        return $this->minValue;
    }

    public function getMaxValue()
    {
        return $this->maxValue;
    }

    public function getMinLength()
    {
        return $this->minLength;
    }

    public function getMaxLength()
    {
        return $this->maxLength;
    }

    public function getDbType()
    {
        return $this->dbType;
    }

    public function getValidValues()
    {
        return $this->validValues;
    }
}
