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
	3: leader not leader
	4: user not registered
	5: user already in team for this game
	6: you already have a pending invite for them
	7: db error
	*/

	$error = -1;

	if (isset($_POST['user'], $_POST['team'], $_SESSION['userid']) && is_numeric($_POST['user']) && is_numeric($_POST['team'])) {
		require_once '../../../includes/db_config.php';
		require_once '../../../includes/user.class.php';
		require_once '../../../includes/team.class.php';

		$leader = LoggedInUser::fromID($_SESSION['userid']);
		$user = User::fromID($_POST['user']);
		$team = new Team($_POST['team']);

		$error = validate($leader, $user, $team);

		if ($error === 0) {
			DB::beginTransaction();
			try {
				// invite user
				$team->invite($user->getInfo('id'));

				DB::commit();
				$error = 0;
			} catch (Exception $e) {
				error_log("Caught Exception when inviting user: $e");
				DB::rollBack();

				$error = 7;
			}
		}

	} else {
		// invalid parameters
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
			header("Location: /teams/" . $_POST['team'] . "/manage?action=invite&error=" . $error);
		} else {
			header("Location: /");
		}
	}
} catch (Exception $e) {
	error_log("Caught Exception when inviting user: $e");
	header(isset($_POST['team']) ? "Location: /teams/".$_POST['team']. "/manage?error" : "/teams?error");
}


function validate($leader, $user, $team) {
	// is leader registered
	if (!$leader->getInfo('registered') || !$leader->verified()) {
		return 2;
	}

	// does leader lead team
	if (!$team->getInfo('exists') || !$team->isLeader($leader->getInfo('id'))) {
		return 3;
	}

	// is user registered
	if (!$user->getInfo('registered') || !$user->verified()) {
		return 4;
	}

	$game = $team->getInfo('game');

	// does user already have a team for this game
	if ($user->hasTeamForGame($game)) {
		return 5;
	}

	$invite_exists = false;
	
	foreach ($team->getInvites('active') as $invite) {
		$invite_exists = ($invite['userid'] == $user->getInfo('id')) || $invite_exists;
	}

	if ($invite_exists) {
		return 6;
	}

	return 0;
}