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

date_default_timezone_set('UTC');

$directory = "assets/stylesheets";

require "../includes/global_constants_variables.php";
require "../includes/cachebuster.php";
require "../vendor/autoload.php";
require "../vendor/leafo/scssphp/example/Server.php";

$scss = new \Leafo\ScssPhp\Compiler();
$scss->setImportPaths($directory);

//adds a usable scss function for cachebusting inside stylesheets
$scss->registerFunction("cacheBust", function($args) {
	$asset = $args[0][2][0];
	return "url(\"" . cacheBust($asset) . "\")";
});

$server = new \Leafo\ScssPhp\Server($directory, null, $scss);
$server->serve();

?>
