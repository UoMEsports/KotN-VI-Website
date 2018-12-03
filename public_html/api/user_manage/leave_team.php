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
	2: user not registered
	3: user not in team, or is the leader
	*/

	$error = -1;

	if (isset($_POST['team'], $_SESSION['userid']) && is_numeric($_POST['team'])) {
		require_once '../../../includes/db_config.php';
		require_once '../../../includes/user.class.php';
		require_once '../../../includes/team.class.php';

		$team = new Team($_POST['team']);
		$user = LoggedInUser::fromID($_SESSION['userid']);

		$error = validate($user, $team);

		if (!$error) {
			DB::beginTransaction();
			try {
				// remove user from `user-teams`
				$stmt = DB::prepare("DELETE FROM `user-teams` WHERE `userid` = ?");
				$stmt->execute([$user->getInfo('id')]);

				DB::commit();
				$error = 0;
			} catch (Exception $e) {
				error_log("Caught Exception when leaving team: $e");
				DB::rollBack();
				$error = 4;
			}
		}
	} else {
		$error = 1;
	}

	// Error handling
	if (!$error) {
		header('Location: /userhome');
	} elseif (isset($_POST['team'])) {
		header('Location: /userhome?action=leave&error=' . $error);
	} else {
		header('Location: /');
	}

} catch (Exception $e) {
	error_log("Caught Exception when leaving team: $e");
	header("Location: /userhome?error");
}

function validate($user, $team) {
	if (!$user->getInfo('registered') || !$user->verified()) {
		return 2;
	}

	if (!$team->getInfo('exists') || !$team->isMember($user->getInfo('id')) || $team->isLeader($user->getInfo('id'))) {
		return 3;
	}

	return 0;
}

?>
