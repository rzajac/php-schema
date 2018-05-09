<?php declare(strict_types=1);
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

use Kicaj\Schema\SchemaEx;

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
    public function getName(): string;

    /**
     * Return table type.
     *
     * @return string The one of the TableItf::TYPE_* values.
     */
    public function getType(): string;

    /**
     * Return table columns.
     *
     * Associative array columnName => Column
     *
     * @return ColumnItf[]
     */
    public function getColumns(): array;

    /**
     * Return table column by its name.
     *
     * @param string $columnName The column name.
     *
     * @throws SchemaEx
     *
     * @return ColumnItf
     */
    public function getColumnByName(string $columnName): ColumnItf;

    /**
     * Get table indexes.
     *
     * Associative array indexName => Column
     *
     * @return IndexItf[]
     */
    public function getIndexes(): array;

    /**
     * Return table index by its name.
     *
     * @param string $indexName The index name.
     *
     * @throws SchemaEx
     *
     * @return IndexItf
     */
    public function getIndexByName(string $indexName): IndexItf;

    /**
     * Return index constraints.
     *
     * Associative array constraintName => Constraint
     *
     * @return ConstraintItf[]
     */
    public function getConstraints(): array;

    /**
     * Get primary key index.
     *
     * @return IndexItf|null
     */
    public function getPrimaryKey();

    /**
     * Get drop statement for the table.
     *
     * @throws SchemaEx
     *
     * @return string
     */
    public function getDropStatement(): string;

    /**
     * Get create statement for the table.
     *
     * @param bool $addIfNotExists Set to true to add if not exists condition.
     *
     * @throws SchemaEx
     *
     * @return string
     */
    public function getCreateStatement(bool $addIfNotExists = false): string;
}
