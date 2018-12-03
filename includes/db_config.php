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

require_once 'db.php';
require_once 'db_settings.php';
require_once 'global_constants_variables.php';

try {
	// DSNs can be added as necessary in db.php
	DB::init([
		'driver'	=> 'mysql',
		'host'		=> constant('DB_HOST_' . PUBLISH_STATE),
		'port'		=> constant('DB_PORT_' . PUBLISH_STATE),
		'username'	=> constant('DB_USERNAME_' . PUBLISH_STATE),
		'password'	=> constant('DB_PASSWORD_' . PUBLISH_STATE),
		'database'	=> constant('DB_NAME_' . PUBLISH_STATE),
		'options'	=> constant('DB_OPTIONS_' . PUBLISH_STATE)
	]);
} catch (Exception $e) {
	error_log("Caught $e");
	http_response_code(500);
	exit;
}
