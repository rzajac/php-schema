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

use Kicaj\Schema\Database\Driver\MySQL;

/**
 * Database table definition.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class TableDefinition
{
    /**
     * Column definitions.
     *
     * @var ColumnDefinition[]
     */
    protected $columns = [];

    /**
     * The table name.
     *
     * @var string
     */
    protected $tableName;

    protected $type;

    /**
     * Primary key column names.
     *
     * [indexName, indexType, [columnNames, ...]]
     *
     * @var array
     */
    protected $primaryKey = [];

    /**
     * Other table indexes.
     *
     * Array of arrays:
     *
     * [indexName, indexType, [columnNames, ...]]
     *
     * @var array
     */
    protected $indexes = [];

    /**
     * TableDefinition constructor.
     *
     * @param string $tableName The database table name.
     * @param string $type      The table or view. One of the SchemaGetter::CREATE_TYPE_* constants.
     */
    public function __construct($tableName, $type = SchemaGetter::CREATE_TYPE_TABLE)
    {
        $this->tableName = $tableName;
        $this->type = $type;
    }

    /**
     * Return table type.
     *
     * @return string The one of the SchemaGetter::CREATE_TYPE_* values.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Make.
     *
     * @param string $tableName The database table name
     * @param string $type      The table or view. One of the SchemaGetter::CREATE_TYPE_* constants.
     *
     * @return static
     */
    public static function make($tableName, $type = SchemaGetter::CREATE_TYPE_TABLE)
    {
        return new static($tableName, $type);
    }

    /**
     * Add database column definition to the table.
     *
     * @param ColumnDefinition $colDef
     *
     * @return ColumnDefinition
     */
    public function addColumn(ColumnDefinition $colDef)
    {
        $this->columns[$colDef->getName()] = $colDef;

        return $this;
    }

    /**
     * Add index definition.
     *
     * The format must be:
     *
     * [indexName, indexType, [$columnName,...]]
     *
     * @param array $indexDefinition
     */
    public function addIndex(array $indexDefinition)
    {
        if ($indexDefinition[1] == MySQL::INDEX_PRIMARY) {
            $this->primaryKey = $indexDefinition;
            foreach ($indexDefinition[2] as $columnName) {
                $this->columns[$columnName]->setIsPartOfPk(true);
            }
        } else {
            $this->indexes[] = $indexDefinition;
        }
    }

    /**
     * Get table indexes.
     *
     * @return array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * Return column definitions.
     *
     * @return ColumnDefinition[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Return table name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->tableName;
    }

    /**
     * Get primary key column names.
     *
     * [indexName, indexType, [columnNames, ...]]
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}
