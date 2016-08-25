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

namespace Kicaj\Schema\Itf;

/**
 * Database table column interface.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
interface ColumnItf
{
    /** PHP types. */
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

    /**
     * Return column name.
     *
     * @return string
     */
    public function getName();

    /**
     * Return table this column belongs to.
     *
     * @return TableItf
     */
    public function getTable();

    /**
     * Returns 0 based position of column in the table.
     *
     * @return int
     */
    public function getPosition();

    /**
     * Is column set as unsigned.
     *
     * @return bool
     */
    public function isUnsigned();

    /**
     * Is null value allowed for the column.
     *
     * @return bool
     */
    public function isNullAllowed();

    /**
     * Is column set as autoincrement.
     *
     * @return bool
     */
    public function isAutoincrement();

    /**
     * Is column part of primary key for the table.
     *
     * @return bool
     */
    public function isPartOfPk();

    /**
     * Return default value for the column.
     *
     * @return mixed Returns null when not set.
     */
    public function getDefaultValue();

    /**
     * Return PHP type assigned to this column.
     *
     * @return string One of the ColumnItf::PHP_TYPE_* constants.
     */
    public function getPhpType();

    /**
     * Return minimum value the column may have.
     *
     * This has meaning only for int, float and date types.
     *
     * @return float|int|string Returns null if not known.
     */
    public function getMinValue();

    /**
     * Return maximum value the column may have.
     *
     * This has meaning only for int, float and date types.
     *
     * @return float|int|string Returns null if not known.
     */
    public function getMaxValue();

    /**
     * Return minimum length for column value.
     *
     * This has meaning only for string types.
     *
     * @return int Returns null if not known.
     */
    public function getMinLength();

    /**
     * Return maximum length for column value.
     *
     * This has meaning only for string types.
     *
     * @return int Returns null if not known.
     */
    public function getMaxLength();

    /**
     * Return database driver name.
     *
     * @return string The one of \Kicaj\DbKit\DbConnector::DB_DRIVER_* constants.
     */
    public function getDriverName();

    /**
     * Get the database specific type of this column.
     *
     * @return string
     */
    public function getDbType();

    /**
     * Return valid values for the column.
     *
     * This has meaning only for sets and enums.
     *
     * @return array|null Returns null if not applicable.
     */
    public function getValidValues();
}
