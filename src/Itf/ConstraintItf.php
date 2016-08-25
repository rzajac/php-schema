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
interface ConstraintItf
{
    /**
     * Return constraint name.
     *
     * @return string
     */
    public function getName();

    /**
     * Return table this index constraint belongs to.
     *
     * @return TableItf
     */
    public function getTable();

    /**
     * Return the index the constraint is on.
     *
     * @return IndexItf
     */
    public function getIndex();

    /**
     * Return the foreign table name.
     *
     * @return string
     */
    public function getForeignTableName();

    /**
     * Return the foreign index name.
     *
     * @return string
     */
    public function getForeignIndexName();
}
