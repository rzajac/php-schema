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

use Kicaj\Schema\SchemaException;

/**
 * Database table interface.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
interface TableItf
{
    /** Table. */
    const TYPE_TABLE = 'table';

    /** View. */
    const TYPE_VIEW = 'view';

    /** Unknown. */
    const TYPE_NONE = '';

    /**
     * Return table name.
     *
     * @return string
     */
    public function getName();

    /**
     * Return table type.
     *
     * @return string The one of the TableItf::TYPE_* values.
     */
    public function getType();

    /**
     * Return table columns.
     *
     * Associative array columnName => Column
     *
     * @return ColumnItf[]
     */
    public function getColumns();

    /**
     * Return table column by its name.
     *
     * @param string $columnName The column name.
     *
     * @throws SchemaException
     *
     * @return ColumnItf
     */
    public function getColumnByName($columnName);

    /**
     * Get table indexes.
     *
     * Associative array indexName => Column
     *
     * @return IndexItf[]
     */
    public function getIndexes();

    /**
     * Return table index by its name.
     *
     * @param string $indexName The index name.
     *
     * @throws SchemaException
     *
     * @return ColumnItf
     */
    public function getIndexByName($indexName);

    /**
     * Return index constraints.
     *
     * Associative array constraintName => Constraint
     *
     * @return ConstraintItf[]
     */
    public function getConstraints();

    /**
     * Get primary key index.
     *
     * @return IndexItf|null
     */
    public function getPrimaryKey();

    /**
     * Get drop statement for the table.
     *
     * @throws SchemaException
     *
     * @return string
     */
    public function getDropStatement();

    /**
     * Get create statement for the table.
     *
     * @param bool $addIfNotExists Set to true to add if not exists condition.
     *
     * @throws SchemaException
     *
     * @return string
     */
    public function getCreateStatement($addIfNotExists = false);
}
