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
use Kicaj\Schema\Itf\ConstraintItf;
use Kicaj\Schema\Itf\DatabaseItf;
use Kicaj\Schema\Itf\IndexItf;
use Kicaj\Schema\Itf\TableItf;

/**
 * Abstract database table class.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
abstract class DbTable implements TableItf
{
    /**
     * Create statement.
     *
     * @var string
     */
    protected $tableCS;

    /**
     * The database this table belongs to.
     *
     * @var DatabaseItf
     */
    protected $db;

    /**
     * Table name.
     *
     * @var string
     */
    protected $name;

    /**
     * Table type.
     *
     * One of the DatabaseItf::CREATE_TYPE_* constants.
     *
     * @var string
     */
    protected $type;

    /**
     * Table columns.
     *
     * Associative array columnName => Column
     *
     * @var ColumnItf[]
     */
    protected $columns = [];

    /**
     * Table indexes.
     *
     * Associative array indexName => Index
     *
     * @var IndexItf[]
     */
    protected $indexes = [];

    /**
     * Table index constraints.
     *
     * Associative array constraintName => Constraint
     *
     * @var ConstraintItf[]
     */
    protected $constraints = [];

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getColumnByName($columnName)
    {
        if (!isset($this->columns[$columnName])) {
            throw SchemaException::spf('Table %s does not have column %s.', $this->name, $columnName);
        }

        return $this->columns[$columnName];
    }

    public function getIndexes()
    {
        return $this->indexes;
    }

    public function getIndexByName($indexName)
    {
        if (!isset($this->indexes[$indexName])) {
            throw SchemaException::spf('Table %s does not have index %s.', $this->name, $indexName);
        }

        return $this->indexes[$indexName];
    }

    public function getConstraints()
    {
        return $this->constraints;
    }

}
