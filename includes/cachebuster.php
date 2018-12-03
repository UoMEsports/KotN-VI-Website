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

// Entry function
function cacheBust($asset) {
	$hash = getAssetHash($asset);
	if (strpos($asset, '/style.php') === 0) {
		return $asset . "&" . $hash;
	} else {
		return $asset . "?" . $hash;
	}
}

function getAssetHash($asset_path) {
	//always cachebust on dev
	if (PUBLISH_STATE == 'DEV') {
		return rand();
	}

	global $redis;
	if (($hash = $redis->get("cachebuster:" . $asset_path)) !== false) {
		//hash exists already, use it
		return $hash;
	}

	//style sheet handler
	if (strpos($asset_path, '/style.php') === 0) {
		//parse stylesheet name from path
		$ss_name = explode('p=', $asset_path)[1];

		return cacheSS($ss_name, $redis);
	} else {
		return cacheFile($asset_path, $redis);
	}
}

function cacheSS($sheet_name, $redis) {
	require_once "../vendor/autoload.php";

	$import_path = realpath(__DIR__ . '/../public_html/assets/stylesheets');

	//create compiler
	$scss = new \Leafo\ScssPhp\Compiler();
	$scss->setImportPaths($import_path);

	//compile css
	$ss = $scss->compile("@import '" . $sheet_name . "'");

	//hash file, save, and return
	$hash = substr(hash('sha256', $ss), 0,12);
	$redis->set("cachebuster:" . $sheet_name, $hash);
	return $hash;
}

function cacheFile($path, $redis) {
	$public_dir = realpath(__DIR__ . '/../public_html/');
	$full_asset_path = realpath($public_dir . explode("?", $path)[0]);

	if ($full_asset_path === false) {
		//file doesn't exist
		return rand();
	}

	//hash file, save, and return
	$hash = substr(hash_file("sha256", $full_asset_path), 0,12);
	$redis->set("cachebuster:" . $path, $hash);
	return $hash;
}

if (PUBLISH_STATE != 'DEV') {
	require_once "redis.php";
}

?>
