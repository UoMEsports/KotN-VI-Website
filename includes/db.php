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


class DB {
	// static PDO proxy class

	// add DSNs as necessary
	private static $dsn = [
		'mysql' => 'mysql:host={{host}};port={{port}};dbname={{database}}',
		'sqlite' => 'sqlite:{{file}}'
	];

	private static $db;
	public static function init(array $config) {
		$dsn = self::$dsn[$config['driver']];
		foreach ($config as $key => $value) {
			if (!is_array($value)) {
				$dsn = str_replace("{{{$key}}}", $value, $dsn);
			}
		}
		foreach (['username', 'password', 'options'] as $optkey) {
			if (!isset($config[$optkey])) {
				$config[$optkey] = null;
			}
		}

		self::$db = new PDO($dsn, $config['username'],
			$config['password'], $config['options']);
		self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public static function __callStatic(string $name, array $arguments) {
		return call_user_func_array([self::$db, $name], $arguments);
	}

	public static function insert(string $table, array $data) {
		// Convienence method for ::prepare()->execute().
		// This function does *not* cache queries! Use ::prepare yourself for
		// issuing a query several times.
		$values = [];
		$columns = [];
		foreach ($data as $key => $value) {
			$columns[] = "`{$key}`";
			$values[':' . $key] = $value;
		}
		$query = "INSERT INTO `{$table}` (" . implode(", ", $columns) .
			") VALUES(" . implode(", ", array_keys($values)) . ')';
		return self::prepare($query)->execute($values);
	}

	public static function select_id(string $table, int $id) {
		// Convenience method similar to insert. Assumes id column named `id`.
		$statement = self::prepare("SELECT * FROM `{$table}` WHERE `id`=:id");
		$statement->execute([':id' => $id]);
		return $statement->fetch(PDO::FETCH_ASSOC);
	}
}

function database_connection($table = DATABASE) {
	$db = mysqli_init();
	if(!$db) {
		die('<p>Database connection failed</p>');
		return false;
	}
	$db->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
	if(@!$db->real_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, $table, DB_PORT)) {
		$GLOBALS['title']	= 'Database connection failed';
		$GLOBALS['content']	= '<div class="msg error">The database could not be reached, it seems we\'re experiencing techincal difficulties.</div>';
		die('DB connection failed');
		return false;
	} else {
		$db->set_charset('utf8');
		return $db;
	}
}
?>
