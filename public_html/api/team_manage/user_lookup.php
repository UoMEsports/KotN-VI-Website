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

require_once '../../../includes/global_constants_variables.php';
require '../post_base.php';

session_start();

/*Error codes:
-1: unknown error
0: success (nothing found is still success)
1: invalid parameters
2: searcher isn't registered + verified
*/

echo '{';

if (isset($_POST['search'], $_POST['team'], $_SESSION['userid']) && is_numeric($_POST['team'])) {
	require_once '../../../includes/user.class.php';
	require_once '../../../includes/team.class.php';
	require_once '../../../includes/db_config.php';

	$search = trim($_POST['search']);
	$search = strtolower($search);
	$team = new Team($_POST['team']);
	searchByNick($search, $team);

} else {
	echo '"error": "1"';
}

echo '}';


function searchById($id) {
	$user = User::fromID($id);
	$leader = User::fromID($_SESSION['userid']);

	if (!$leader->getInfo('registered') || !$leader->verified()) {
		echo '"error": "2"';
		return false;
	}

	echo '"error": "0", "results": [';

	if (!$user->getInfo('registered') || !$user->verified()) {
		echo ']';
		return false;
	}

	if (!($leader->getInfo('uni') == $user->getInfo('uni'))) {
		return false;
		echo ']';
	}

	if ($user->verified() && $user->getInfo('uni') == $leader->getInfo('uni') && $user->getInfo('id') != $leader->getInfo('id')) {
		echo '{';
			echo '"id": "' . $id . '",';
			echo '"first_name": "' . $user->getInfo('first_name') . '",';
			echo '"nick": "' . $user->getInfo('nick') . '",';
			echo '"last_name": "' . $user->getInfo('last_name') . '",';
			echo '"profile_pic": "' . expandPPURL($user->getInfo('profile_pic'), 'medium') . '"';
		echo '}';
	}
	echo ']';
}

function searchByNick($nick, $team) {
	$leader = User::fromID($_SESSION['userid']);

	$stmt = DB::prepare("SELECT `id` FROM `users` WHERE `nick` LIKE ? ORDER BY `nick` DESC");
	$stmt->execute(['%' . $nick . '%']);

	$users = $stmt->fetchAll();

	echo '"error": "0", "results": [';

	$results = "";

	foreach ($users as $userid) {
		$user = User::fromID($userid['id']);

		if ($user->verified() && ($user->getInfo('uni') == $leader->getInfo('uni')) && $user->getInfo('id') != $leader->getInfo('id')) {

			// check that user doesn't have a pending invite to this team
			$invite_exists = false;

			foreach ($team->getInvites('active') as $invite) {
				$invite_exists = ($invite['userid'] == $user->getInfo('id')) || $invite_exists;
			}

			if (!$invite_exists) {
				$results .= '{';
					$results .= '"id": "' . $user->getInfo('id') . '",';
					$results .= '"first_name": "' . $user->getInfo('first_name') . '",';
					$results .= '"nick": "' . $user->getInfo('nick') . '",';
					$results .= '"last_name": "' . $user->getInfo('last_name') . '"';
				$results .= '},';
			}
		}
	}

	// removing any trailing commas is the quickest way I could think of doing this
	$results = rtrim($results, ",");

	echo $results;

	echo ']';
	
}