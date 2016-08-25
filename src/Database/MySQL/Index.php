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

namespace Kicaj\Schema\Database\MySQL;

use Kicaj\Schema\DbIndex;
use Kicaj\Schema\Itf\TableItf;
use Kicaj\Schema\SchemaException;
use Kicaj\Tools\Helper\Str;

/**
 * MySQL index.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Index extends DbIndex
{
    /**
     * Constructor.
     *
     * @param string   $indexDef The index definition.
     * @param TableItf $table    The table index belongs to.
     *
     * @throws SchemaException
     */
    public function __construct($indexDef, TableItf $table)
    {
        $this->indexDef = $indexDef;
        $this->table = $table;

        $this->parseIndexDef();
    }

    /**
     * Parse index definition.
     *
     * @throws SchemaException
     */
    protected function parseIndexDef()
    {
        preg_match('/(.*)?KEY (?:`(.*?)` )?\((.*)\)/', $this->indexDef, $matches);

        if (count($matches) != 4) {
            throw new SchemaException('Cannot parse index definition: ' . $this->indexDef);
        }

        $this->type = trim($matches[1]) ?: 'KEY';
        $this->name = trim($matches[2]);

        $colNames = str_replace('`', '', $matches[3]);
        $colNames = str_replace(' ', '', $colNames);
        $this->columnNames = explode(',', $colNames);
    }

    /**
     * Returns true if line is definition of one of the indexes.
     *
     * @param string $line The line from CREATE STATEMENT.
     *
     * @return bool
     */
    public static function isIndexDef($line)
    {
        if (Str::startsWith($line, 'PRIMARY KEY')) {
            return true;
        }

        if (Str::startsWith($line, 'UNIQUE KEY')) {
            return true;
        }

        if (Str::startsWith($line, 'KEY')) {
            return true;
        }

        return false;
    }
}
