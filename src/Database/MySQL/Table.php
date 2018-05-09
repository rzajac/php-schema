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

namespace Kicaj\Schema\Database\MySQL;

use Kicaj\Schema\DbTable;
use Kicaj\Schema\Itf\ColumnItf;
use Kicaj\Schema\Itf\ConstraintItf;
use Kicaj\Schema\Itf\DatabaseItf;
use Kicaj\Schema\Itf\IndexItf;
use Kicaj\Schema\Itf\TableItf;
use Kicaj\Schema\SchemaEx;
use Kicaj\Tools\Helper\Str;

/**
 * MySQL Table.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Table extends DbTable
{
    /**
     * TableDef constructor.
     *
     * @param string      $tableCS The table create statement.
     * @param DatabaseItf $db      The database interface.
     *
     * @throws SchemaEx
     */
    public function __construct(string $tableCS, DatabaseItf $db)
    {
        $this->tableCS = $tableCS;
        $this->db = $db;

        $this->parseTableCS();
    }

    /**
     * Parse table create statement.
     *
     * @throws SchemaEx
     */
    protected function parseTableCS()
    {
        $lines = explode("\n", $this->tableCS);

        foreach ($lines as $index => $line) {
            $line = trim($line);
            // Column definitions start with `
            if (0 === strpos($line, '`')) {
                $this->addColumn(new Column($line, $index, $this));
            } elseif (Index::isIndexDef($line)) {
                $this->addIndex(new Index($line, $this));
            } elseif (Constraint::isConstraintDef($line)) {
                $this->addConstraint(new Constraint($line, $this));
            } elseif (preg_match('/CREATE TABLE `(.*?)`/', $line, $matches) === 1) {
                $this->name = $matches[1];
                $this->type = TableItf::TYPE_TABLE;
            } elseif (preg_match('/VIEW `(.*?)` AS /i', $line, $matches) === 1) {
                $this->name = $matches[1];
                $this->type = TableItf::TYPE_VIEW;
            }
        }
    }

    /**
     * Add database column definition.
     *
     * The addition order is significant.
     *
     * @param ColumnItf $column
     */
    public function addColumn(ColumnItf $column)
    {
        $this->columns[$column->getName()] = $column;
    }

    /**
     * Add index definition.
     *
     * @param IndexItf $index
     *
     * @throws SchemaEx
     */
    public function addIndex(IndexItf $index)
    {
        $this->indexes[$index->getName()] = $index;

        if ($index->getType() == IndexItf::PRIMARY) {
            /** @var Column $column */
            foreach ($index->getColumns() as $column) {
                $column->setPartOfPk();
            }
        }
    }

    /**
     * Add index constraint.
     *
     * @param ConstraintItf $constraint
     */
    public function addConstraint(ConstraintItf $constraint)
    {
        $this->constraints[$constraint->getName()] = $constraint;
    }

    public function getDropStatement(): string
    {
        $sql = '';
        switch ($this->type) {
            case TableItf::TYPE_TABLE:
                $sql = 'DROP TABLE IF EXISTS `' . $this->name . '`;';
                break;

            case TableItf::TYPE_VIEW:
                $sql = 'DROP VIEW IF EXISTS `' . $this->name . '`;';
        }

        return $sql;
    }

    /**
     * Fix and rewrite CREATE statements if needed.
     *
     * @param bool $addIfNotExists Set to true to add if not exists condition.
     *
     * @return string The table create statement.
     */
    protected function fixCreateStatement(bool $addIfNotExists = false): string
    {
        $tableCS = '';
        switch ($this->type) {
            case TableItf::TYPE_TABLE:
                $tableCS = $this->fixTableCreateStatement($addIfNotExists);
                break;

            case TableItf::TYPE_VIEW:
                $tableCS = $this->fixViewCreateStatement($addIfNotExists);
                break;
        }

        return $tableCS;
    }

    /**
     * Fix and rewrite CREATE statements if needed.
     *
     * @param bool $addIfNotExists Set to true to add if not exists condition.
     *
     * @return string The table create statement.
     */
    protected function fixViewCreateStatement(bool $addIfNotExists = false): string
    {
        $tableCS = $this->tableCS;

        if (!(Str::endsWith(trim($this->tableCS), ';'))) {
            $tableCS = trim($this->tableCS) . ";";
        }

        if (!$addIfNotExists) {
            return $tableCS;
        }

        return preg_replace('/CREATE/', 'CREATE OR REPLACE', $tableCS);
    }

    /**
     * Fix and rewrite table CREATE statement if needed.
     *
     * @param bool $addIfNotExists Set to true to add if not exists condition.
     *
     * @return string The table create statement.
     */
    protected function fixTableCreateStatement(bool $addIfNotExists = false): string
    {
        $tableCS = $this->tableCS;

        if (!(Str::endsWith(trim($this->tableCS), ';'))) {
            $tableCS = trim($this->tableCS) . ";";
        }

        $tableCS = preg_replace('/(AUTO_INCREMENT=)([0-9]+)/', '${1}1', $tableCS);

        if (!$addIfNotExists) {
            return $tableCS;
        }

        $tableCS = preg_replace('/CREATE TABLE/', 'CREATE TABLE'.' IF NOT EXISTS', $tableCS);

        return $tableCS;
    }

    public function getPrimaryKey()
    {
        return array_key_exists('', $this->indexes) ? $this->indexes[''] : null;
    }

    public function getCreateStatement(bool $addIfNotExists = false): string
    {
        return $this->fixCreateStatement($addIfNotExists);
    }
}
