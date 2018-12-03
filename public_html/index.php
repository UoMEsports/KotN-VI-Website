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

require_once '../includes/global_constants_variables.php';
require_once '../includes/db_config.php';
require_once '../includes/cachebuster.php';
require_once '../includes/user.class.php';

session_start();

$loggedInUser = false;

if (isset($_SESSION['userid'])) {
	if (isset($_GET['logout'])) {
		unset($_SESSION['userid']);
		header("Location: /");
		exit;
	}

	$loggedInUser = LoggedInUser::fromID($_SESSION["userid"]);
	if (!$loggedInUser->getInfo('registered')) {
		unset($_SESSION['userid']);
		header("Location: /" . $page);
	}
}

//get all uri elements as array `$url_parts` (e.g. '/monthlies/na' becomes ["monthlies", "na"])
$url = ltrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if($url === '') $url = 'home';
$url_parts = explode('/', $url);

$page = '';
$consumed = 0;

//loop through url parts and determine which is the physical file we're after
foreach($url_parts as $part) {
	$part = ltrim(strtolower($part), '.');
	if(empty($part)) continue;
	$nextpage = $page . '/' . ltrim(strtolower($part), '.');
	$base = __DIR__ . '/../pages' . $nextpage;
	if(!file_exists($base . '.php') && !is_dir($base)) break;
	$page = $nextpage;
	$consumed++;
}

$base = __DIR__ . '/../pages' . $page;
if(!file_exists($base . '.php') && is_dir($base)) {
	$page .= '/index';
}

//final page path
$page = ltrim($page, "/");

//create array of $url_parts found after the file path itself (used for /newsfeed filters)
$page_args = array_slice($url_parts, $consumed);

// Set default values for title & content
// Overwrite if a page is available
$html_title = "King of the North VI";

//capture the output of a function into an element of `$content_targets`
$content_targets = [];
function capture($into, $fn) {
	global $content_targets;
	ob_start();
	$blob = "";
	try {
		call_user_func($fn);
	} finally {
		$blob = ob_get_contents();
		ob_end_clean();
	}
	$old = $content_targets[$into] ?? "";
	$content_targets[$into] = $old . $blob;
}

//retreive element from `$content_targets`
function get_contents($from) {
	global $content_targets;
	return isset($content_targets[$from]) ? $content_targets[$from] : '';
}

//include and return content from desired page if it exists
function include_page() {
	global $page, $page_args, $page_name, $html_title; 

	$fname = __DIR__ . '/../pages/' . $page . '.php';
	if(!empty($page) && file_exists($fname)) {
		include $fname;
	} else {
		header("HTTP/1.0 404 Not Found");
		include_once '../pages/404.php';
	}
}

ob_start();
capture("content", 'include_page');
ob_end_clean();

$content = get_contents("content");

// Output the default if $content is filled;
if(!empty($content)) {
	include_once '../includes/content_template.php';
}
?>
