<?php
/**
 * Copyright (C) 2018 Daniel Shields
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


define('DB_HOST_LIVE',     'localhost');
define('DB_NAME_LIVE',    'REDACTED');
define('DB_USERNAME_LIVE', 'REDACTED');
define('DB_PASSWORD_LIVE', 'REDACTED');
define('DB_PORT_LIVE',     3306);
define('DB_OPTIONS_LIVE',  [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
]);

define('DB_HOST_BETA',     'localhost');
define('DB_NAME_BETA',    'REDACTED');
define('DB_USERNAME_BETA', 'REDACTED');
define('DB_PASSWORD_BETA', 'REDACTED');
define('DB_PORT_BETA',     3306);
define('DB_OPTIONS_BETA',  [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
]);

define('DB_HOST_DEV',     'localhost');
define('DB_NAME_DEV',    'uom_kotn_vi');
define('DB_USERNAME_DEV', 'root');
define('DB_PASSWORD_DEV', NULL);
define('DB_PORT_DEV',     3306);
define('DB_OPTIONS_DEV',  [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
]);
