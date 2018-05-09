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

namespace Kicaj\Schema\Database;

/**
 * Database connection helper class.
 *
 * @author Rafal Zajac <rzajac@gmail.com>
 */
final class DbConnect
{
    /**
     * Get database configuration array.
     *
     * @param string $driver   The database driver to use. One of the DbConnector::DB_DRIVER_* constants.
     * @param string $host     The database host address.
     * @param string $username The username.
     * @param string $password The password.
     * @param string $database The database name.
     * @param int    $port     The database port.
     * @param bool   $connect  Set to true to connect right away.
     * @param string $timezone The timezone to use for the connection.
     * @param bool   $debug    Set to true to enable debugging.
     *
     * @return array
     */
    public static function getCfg(
        string $driver,
        string $host,
        string $username,
        string $password,
        string $database,
        int $port,
        bool $connect = true,
        string $timezone = '',
        bool $debug = false
    ): array {
        if ($timezone == '') {
            $timezone = 'UTC';
        }

        return [
            DbConnector::DB_CFG_DRIVER => $driver,
            DbConnector::DB_CFG_HOST => $host,
            DbConnector::DB_CFG_USERNAME => $username,
            DbConnector::DB_CFG_PASSWORD => $password,
            DbConnector::DB_CFG_DATABASE => $database,
            DbConnector::DB_CFG_PORT => $port,
            DbConnector::DB_CFG_CONNECT => $connect,
            DbConnector::DB_CFG_TIMEZONE => $timezone,
            DbConnector::DB_CFG_DEBUG => $debug,
        ];
    }

    /**
     * Get database driver name.
     *
     * @param array $config
     *
     * @return string One of the DbConnector::DB_DRIVER_* constants.
     */
    public static function getDriver(array $config): string
    {
        return $config[DbConnector::DB_CFG_DRIVER];
    }
}
