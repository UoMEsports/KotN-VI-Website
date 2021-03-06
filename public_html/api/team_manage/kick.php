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


try {
	require_once '../../../includes/global_constants_variables.php';
	require '../post_base.php';

	session_start();

	/*Error codes:
	-1: unknown error
	0: success
	1: invalid parameters
	2: leader not registered
	3: leader not leader of a team
	4: user not registered
	5: user not in leader's team
	*/

	$error = -1;

	if (isset($_POST['user'], $_POST['team'], $_SESSION['userid']) && is_numeric($_POST['team']) && is_numeric($_POST['user'])) {
		require_once '../../../includes/db_config.php';
		require_once '../../../includes/user.class.php';
		require_once '../../../includes/team.class.php';

		$leader = LoggedInUser::fromID($_SESSION['userid']);
		$team = new Team($_POST['team']);
		$user = User::fromID($_POST['user']);

		$error = validate($leader, $user, $team);

		if ($error === 0) {
			DB::beginTransaction();
			try {
				// remove user from team
				$team->kick($user->getInfo('id'));

				DB::commit();
				$error = 0;
			} catch (Exception $e) {
				error_log("Caught Exception when kicking user: $e");
				DB::rollBack();
				$error = 6;
			}
		}
	} else {
		$error = 1;
	}

	if (!$error) {
		if (isset($_POST['team'])) {
			header("Location: /teams/" . $_POST['team'] . "/manage");
		} else {
			header("Location: /");
		}
	} else {
		if (isset($_POST['team'])) {
			header("Location: /teams/" . $_POST['team'] . "/manage?action=kick&error=" . $error);
		} else {
			header("Location: /");
		}
	}
	
} catch (Exception $e) {
	error_log("Caught Exception when kicking user: $e");
	header(isset($_POST['team']) ? "Location: /teams/".$_POST['team']. "/manage?error" : "/teams?error");
}


function validate($leader, $user, $team) {
	// leader registered
	if (!$leader->getInfo('registered') || !$leader->verified()) {
		return 2;
	}
	// team exists and leader is leader
	if (!$team->getInfo('exists') || !$team->isLeader($leader->getInfo('id'))) {
		return 3;
	}
	// user registered
	if (!$user->getInfo('registered') || !$user->verified()) {
		return 4;
	}
	// user in a team and is the same team as leader
	if (!$team->isMember($user->getInfo('id'))) {
		return 5;
	}
	return 0;
}

?>
