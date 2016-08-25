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

use Kicaj\DbKit\DbConnector;
use Kicaj\Schema\DbColumn;
use Kicaj\Schema\Itf\ColumnItf;
use Kicaj\Schema\Itf\TableItf;
use Kicaj\Schema\SchemaException;

/**
 * MySQL column.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
class Column extends DbColumn
{
    /**
     * Constructor.
     *
     * @param string   $columnDef The column definition as returned by SHOW CREATE TABLE query.
     * @param int      $index     The zero based index of the column in the table.
     * @param TableItf $table     The database table this column belongs to.
     *
     * @throws SchemaException
     */
    public function __construct($columnDef, $index, TableItf $table)
    {
        $this->columnDef = trim($columnDef);
        $this->index = $index;
        $this->table = $table;

        $this->typeMap = [
            MySQL::TYPE_TINYINT   => ColumnItf::PHP_TYPE_INT,
            MySQL::TYPE_SMALLINT  => ColumnItf::PHP_TYPE_INT,
            MySQL::TYPE_MEDIUMINT => ColumnItf::PHP_TYPE_INT,
            MySQL::TYPE_INT       => ColumnItf::PHP_TYPE_INT,
            MySQL::TYPE_BIGINT    => ColumnItf::PHP_TYPE_INT,
            MySQL::TYPE_DECIMAL   => ColumnItf::PHP_TYPE_INT,
            MySQL::TYPE_BIT       => ColumnItf::PHP_TYPE_BOOL,

            MySQL::TYPE_BINARY    => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_VARBINARY => ColumnItf::PHP_TYPE_STRING,

            MySQL::TYPE_CHAR       => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_VARCHAR    => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_TEXT       => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_TINYTEXT   => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_LONGTEXT   => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_MEDIUMTEXT => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_BLOB       => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_LONGBLOB   => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_MEDIUMBLOB => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_TINYBLOB   => ColumnItf::PHP_TYPE_STRING,

            MySQL::TYPE_FLOAT  => ColumnItf::PHP_TYPE_FLOAT,
            MySQL::TYPE_DOUBLE => ColumnItf::PHP_TYPE_FLOAT,

            MySQL::TYPE_ENUM => ColumnItf::PHP_TYPE_STRING,
            MySQL::TYPE_SET  => ColumnItf::PHP_TYPE_STRING,

            MySQL::TYPE_TIMESTAMP => ColumnItf::PHP_TYPE_TIMESTAMP,
            MySQL::TYPE_DATETIME  => ColumnItf::PHP_TYPE_DATETIME,
            MySQL::TYPE_DATE      => ColumnItf::PHP_TYPE_DATE,
            MySQL::TYPE_TIME      => ColumnItf::PHP_TYPE_TIME,
            MySQL::TYPE_YEAR      => ColumnItf::PHP_TYPE_YEAR,
        ];

        $this->parseColumn();
    }

    /**
     * Parse database column definition.
     *
     * @throws SchemaException
     */
    protected function parseColumn()
    {
        // Explode. First two indexes are always column name and its type.
        $definition = explode(' ', $this->columnDef);
        $this->name = trim($definition[0], '`');
        $this->dbType = $this->parseMySQLType($definition[1]);

        $colExtra = '';
        // If more then 2 we have extra definitions for the column.
        if (2 < count($definition)) {
            // We need it as a string.
            $colExtra = trim(implode(' ', array_slice($definition, 2)), ',');
        }

        // Figure out the extra parameters first so we can access to undefined not null and so on.
        $this->parseAndSetColExtra($colExtra);

        // Special handling of enums and sets.
        if (in_array($this->dbType, [MySQL::TYPE_ENUM, MySQL::TYPE_SET])) {
            $this->setLengthsAndValidValues($definition[1] . ' ' . $colExtra);
        } else {
            $this->setLengthsAndValidValues($definition[1]);
        }

        $this->setTypeBounds();
    }

    /**
     * Parse MySQL column type.
     *
     * @param string $dbType The database type definition.
     *
     * @throws SchemaException
     *
     * @return string The one of \Kicaj\Schema\Database\MySQL\MySQL::TYPE_* constants.
     */
    protected function parseMySQLType($dbType)
    {
        preg_match('/^([a-z]+)(?:\(.*?\))?/', $dbType, $matches);

        if (2 != count($matches)) {
            throw new SchemaException('Could not parse type: ' . $dbType);
        }

        if (!in_array($matches[1], array_keys($this->typeMap))) {
            throw new SchemaException('Unsupported type: ' . $matches[1]);
        }

        return $matches[1];
    }

    /**
     * Parse extra column definitions.
     *
     * @param string $colDefExtra The extra column definitions.
     *
     * @throws SchemaException
     */
    protected function parseAndSetColExtra($colDefExtra)
    {
        $this->parseUnsigned($colDefExtra);

        if (false !== strpos($colDefExtra, 'NOT NULL')) {
            $this->isNullAllowed = false;
        }

        if (false !== strpos($colDefExtra, 'AUTO_INCREMENT')) {
            $this->isAutoincrement = true;
        } elseif ($this->isDbNumberType()) {
            $this->isAutoincrement = false;
        }

        if (false !== strpos($colDefExtra, 'DEFAULT')) {
            preg_match('/DEFAULT (.*)/', $colDefExtra, $matches);
            if (2 != count($matches)) {
                throw new SchemaException('Could not decipher DEFAULT: ' . $colDefExtra);
            }
            $defaultValue = trim($matches[1]);
            $defaultValue = trim($defaultValue, '\'');
            if ($defaultValue == 'NULL') {
                $defaultValue = null;
            }
            $this->setDefaultValue($defaultValue);
        }
    }

    /**
     * Parse extra column definitions.
     *
     * @param string $colDefExtra The extra column definitions.
     *
     * @throws SchemaException
     */
    protected function parseUnsigned($colDefExtra)
    {
        if (!$this->isDbNumberType()) {
            return;
        }

        if ($this->dbType == MySQL::TYPE_YEAR) {
            $this->isUnsigned = true;

            return;
        }

        if (false !== strpos($colDefExtra, 'unsigned')) {
            $this->isUnsigned = true;
        } else {
            $this->isUnsigned = false;
        }
    }

    /**
     * Is database type number type.
     *
     * @return bool
     */
    protected function isDbNumberType()
    {
        // Types that we ignore.
        switch ($this->dbType) {
            case MySQL::TYPE_TINYINT:
            case MySQL::TYPE_SMALLINT:
            case MySQL::TYPE_MEDIUMINT:
            case MySQL::TYPE_INT:
            case MySQL::TYPE_BIGINT:
            case MySQL::TYPE_FLOAT:
            case MySQL::TYPE_DOUBLE:
            case MySQL::TYPE_DECIMAL:
            case MySQL::TYPE_YEAR:
            case MySQL::TYPE_BIT:
                return true;
        }

        return false;
    }

    /**
     * Parse type length values.
     *
     * @param string $dbTypeDef The database type definition.
     */
    protected function setLengthsAndValidValues($dbTypeDef)
    {
        if ($this->isDbNumberType()) {
            return;
        }

        // No parenthesis nothing to do.
        if (false === strpos($dbTypeDef, '(')) {
            return;
        }

        // Everything inside parenthesis.
        preg_match('/.*\((.*)\).*/', $dbTypeDef, $matches);

        // For enums and sets we set array of valid values.
        if (in_array($this->dbType, [MySQL::TYPE_ENUM, MySQL::TYPE_SET])) {
            $matches = explode(',', $matches[1]);
            foreach ($matches as &$value) {
                $value = trim($value, '\'');
            }
            $this->validValues = $matches;

            return;
        }

        if (2 === count($matches)) {
            // NOTE: we ignore some types like decimal(5,2) so we
            // don't check for this kind of strings.
            $lengthDef = $matches[1];
            $left = $lengthDef;
            $this->minLength = 0;
            $this->maxLength = (int)$left;
        }
    }

    /**
     * Set default value for column.
     *
     * @param mixed $defaultValue The default value to set.
     */
    public function setDefaultValue($defaultValue)
    {
        $phpType = $this->getPhpType();
        if (null === $defaultValue) {
            $this->defaultValue = null;

            return;
        }

        switch ($phpType) {
            case ColumnItf::PHP_TYPE_INT:
            case ColumnItf::PHP_TYPE_YEAR:
                $this->defaultValue = (int)$defaultValue;
                break;

            case ColumnItf::PHP_TYPE_FLOAT:
                $this->defaultValue = (float)$defaultValue;
                break;

            case ColumnItf::PHP_TYPE_BOOL:
                $this->defaultValue = (bool)$defaultValue;
                break;

            default:
                $this->defaultValue = $defaultValue;
        }
    }

    /**
     * Set column is part of primary key.
     */
    public function setPartOfPk()
    {
        $this->isPartOfPk = true;
    }

    /**
     * Set type bounds.
     *
     * @throws SchemaException
     */
    protected function setTypeBounds()
    {
        switch ($this->dbType) {
            case MySQL::TYPE_TINYINT:
                if ($this->isUnsigned) {
                    $this->minValue = 0;
                    $this->maxValue = 255;
                } else {
                    $this->minValue = -128;
                    $this->maxValue = 127;
                }
                break;

            case MySQL::TYPE_SMALLINT:
                if ($this->isUnsigned) {
                    $this->minValue = 0;
                    $this->maxValue = 65535;
                } else {
                    $this->minValue = -32768;
                    $this->maxValue = 32767;
                }
                break;

            case MySQL::TYPE_MEDIUMINT:
                if ($this->isUnsigned) {
                    $this->minValue = 0;
                    $this->maxValue = 16777215;
                } else {
                    $this->minValue = -8388608;
                    $this->maxValue = 8388607;
                }
                break;

            case MySQL::TYPE_INT:
                if ($this->isUnsigned) {
                    $this->minValue = 0;
                    $this->maxValue = 4294967295;
                } else {
                    $this->minValue = -2147483648;
                    $this->maxValue = 2147483647;
                }
                break;

            case MySQL::TYPE_BIGINT:
                if ($this->isUnsigned) {
                    $this->minValue = 0;
                    $this->maxValue = 18446744073709551615;
                } else {
                    $this->minValue = -9223372036854775808;
                    $this->maxValue = 9223372036854775807;
                }
                break;

            case MySQL::TYPE_TINYTEXT:
                $this->minLength = 0;
                $this->maxLength = 255;
                break;

            case MySQL::TYPE_TEXT:
                $this->minLength = 0;
                $this->maxLength = 65535;
                break;

            case MySQL::TYPE_MEDIUMTEXT:
                $this->minLength = 0;
                $this->maxLength = 16777215;
                break;

            case MySQL::TYPE_LONGTEXT:
                $this->minLength = 0;
                $this->maxLength = 4294967295;
                break;

            case MySQL::TYPE_TINYBLOB:
                $this->minLength = 0;
                $this->maxLength = 255;
                break;

            case MySQL::TYPE_MEDIUMBLOB:
                $this->minLength = 0;
                $this->maxLength = 16777215;
                break;

            case MySQL::TYPE_BLOB:
                $this->minLength = 0;
                $this->maxLength = 65535;
                break;

            case MySQL::TYPE_LONGBLOB:
                $this->minLength = 0;
                $this->maxLength = 4294967295;
                break;

            case MySQL::TYPE_TIMESTAMP:
                $this->minValue = 0;
                $this->maxValue = 2147483647;
                break;

            case MySQL::TYPE_DATE:
                $this->minValue = '1000-01-01';
                $this->maxValue = '9999-12-31';
                break;

            case MySQL::TYPE_DATETIME:
                $this->minValue = '1000-01-01 00:00:00';
                $this->maxValue = '9999-12-31 23:59:59';
                break;

            case MySQL::TYPE_YEAR:
                $this->minValue = 1901;
                $this->maxValue = 2155;
                break;

            case MySQL::TYPE_FLOAT:
            case MySQL::TYPE_DOUBLE:
            case MySQL::TYPE_DECIMAL:
                if ($this->isUnsigned) {
                    $this->minValue = 0;
                    $this->maxValue = null;
                } else {
                    $this->minValue = null;
                    $this->maxValue = null;
                }
                break;

            case MySQL::TYPE_BIT:
                $this->minValue = 0;
                $this->maxValue = 1;
                break;

            case MySQL::TYPE_TIME:
                $this->minValue = '00:00:00';
                $this->maxValue = '23:59:59';
                break;

            case MySQL::TYPE_BINARY:
            case MySQL::TYPE_VARBINARY:
            case MySQL::TYPE_CHAR:
            case MySQL::TYPE_VARCHAR:
            case MySQL::TYPE_ENUM:
            case MySQL::TYPE_SET:
                // No bounds
                break;
        }
    }

    public function getDriverName()
    {
        return DbConnector::DB_DRIVER_MYSQL;
    }
}
