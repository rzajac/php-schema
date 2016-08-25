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
 * Database table index interface.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
interface IndexItf
{
    /** Index types. */
    const PRIMARY = 'PRIMARY';
    const UNIQUE = 'UNIQUE';
    const KEY = 'KEY';

    /**
     * Get index name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get index type.
     *
     * @return string The one of IndexItf::INDEX_* constants.
     */
    public function getType();

    /**
     * Return columns this index is composed of.
     *
     * The order of columns matter.
     *
     * The array is associative:
     *
     * columnName => ColumnItf
     *
     * @return ColumnItf[]
     */
    public function getColumns();

    /**
     * Return table this index belongs to.
     *
     * @return TableItf
     */
    public function getTable();
}
