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
 * Interface for getting database schema information.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
interface DatabaseItf
{
    /**
     * Get database table names.
     *
     * @throws SchemaEx
     *
     * @return string[] The table names.
     */
    public function dbGetTableNames(): array;

    /**
     * Get database view names.
     *
     * @throws SchemaEx
     *
     * @return string[] The view names.
     */
    public function dbGetViewNames(): array;

    /**
     * Return table definition for given database table.
     *
     * @param string $tableName The database table name.
     *
     * @throws SchemaEx
     *
     * @return TableItf
     */
    public function dbGetTableDefinition(string $tableName): TableItf;

    /**
     * Initialize table from create statement.
     *
     * @param string $tableCS The table create statement.
     *
     * @throws SchemaEx
     *
     * @return TableItf
     */
    public function initTable(string $tableCS): TableItf;
}
