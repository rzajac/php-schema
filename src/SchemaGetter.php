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

use Kicaj\Tools\Db\DatabaseException;
use Kicaj\Tools\Db\DbConnector;

/**
 * Get database schema interface.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
interface SchemaGetter extends DbConnector
{
    /** Table create statement */
    const CREATE_TYPE_TABLE = 'table';

    /** View create statement */
    const CREATE_TYPE_VIEW = 'view';

    /** Unknown sreate statement */
    const CREATE_TYPE_NONE = '';

    /**
     * Get database table names.
     *
     * @throws DatabaseException
     *
     * @return string[] The table names
     */
    public function dbGetTableNames();

    /**
     * Get database table drop command.
     *
     * @param string $tableName The table name
     * @param string $type      The table or view. Onr of the self:: CREATE_TYPE_* constants.
     *
     * @throws SchemaException
     *
     * @return string
     */
    public function dbGetTableDropCommand($tableName, $type);

    /**
     * Get create statement for the given table name.
     *
     * Method returns associative array where keys are table names and values are arrays with keys:
     *
     * - create - CREATE TABLE or VIEW statement
     * - type   - table, view ( one of the self::CREATE_TYPE_* )
     * - name   - table name
     *
     * @param string $tableName      The table name to get CREATE statement for
     * @param bool   $addIfNotExists Set to true to add IF NOT EXIST to CREATE TABLE
     *
     * @throws DatabaseException
     *
     * @return array
     */
    public function dbGetCreateStatement($tableName, $addIfNotExists = false);

    /**
     * Return table definitions for given database table.
     *
     * @param string $tableName The database table name
     *
     * @throws DatabaseException
     *
     * @return TableDefinition
     */
    public function dbGetTableDefinition($tableName);

    /**
     * Get create statements for all database tables.
     *
     * @param bool $addIfNotExists Set to true to add IF NOT EXIST to CREATE TABLE
     *
     * @throws DatabaseException
     *
     * @return array See SchemaGetter::dbGetCreateStatement
     */
    public function dbGetCreateStatements($addIfNotExists = false);
}
