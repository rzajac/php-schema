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

namespace Kicaj\Schema;


use Kicaj\Schema\Itf\ColumnItf;
use Kicaj\Schema\Itf\IndexItf;
use Kicaj\Schema\Itf\TableItf;

/**
 * Abstract database index class.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
abstract class DbIndex implements IndexItf
{
    /**
     * Index definition.
     *
     * @var string
     */
    protected $indexDef;

    /**
     * The table index belongs to.
     *
     * @var TableItf
     */
    protected $table;

    /**
     * The index name.
     *
     * @var string
     */
    protected $name;

    /**
     * The index type.
     *
     * @var string
     */
    protected $type;

    /**
     * The list of columns this index is composed of.
     *
     * The order of the array does matter.
     *
     * @var string[]
     */
    protected $columnNames = [];

    /**
     * The columns the index is composed of.
     *
     * The order in the array does matter.
     *
     * The array is associative:
     *
     * columnName => ColumnItf
     *
     * @var ColumnItf[]
     */
    protected $columns = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTable(): TableItf
    {
        return $this->table;
    }

    public function getColumns(): array
    {
        if (!$this->columns) {
            foreach ($this->columnNames as $colName) {
                $column = $this->getTable()->getColumnByName($colName);
                $this->columns[$column->getName()] = $column;
            }
        }

        return $this->columns;
    }

    /**
     * Return names of columns this index is composed of.
     *
     * The order in the array does matter.
     *
     * @return string[]
     */
    public function getColumnNames(): array
    {
        return $this->columnNames;
    }
}
