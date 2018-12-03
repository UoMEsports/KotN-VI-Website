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
	3: leader not leader of team
	4: not enough players
	5: db error

	*/

	$error = -1;

	if (isset($_POST['date'], $_POST['team'], $_SESSION['userid']) && is_numeric($_POST['team'])) {
		require_once '../../../includes/db_config.php';
		require_once '../../../includes/user.class.php';
		require_once '../../../includes/team.class.php';

		$leader = LoggedInUser::fromID($_SESSION['userid']);
		$date = trim($_POST['date']);
		$team = new Team($_POST['team']);

		$error = validate($leader, $date, $team);

		if ($error === 0) {
			try {
				$stmt = DB::prepare("UPDATE `teams` SET `qual`=? WHERE `id` = ?");
				$stmt->execute([$date, $team->getInfo('id')]);

				$error = 0;

			} catch (Exception $e) {
				error_log("Caught Exception when processing quali signup: $e");
				DB::rollBack();
				$error = 5;
			}
		}
	} else {
		$error = 1;
	}

	if (!$error && isset($_POST['team'])) {
		header("Location: /teams/" . $_POST['team'] . "/manage");
	} elseif (isset($_POST['team'])) {
		header("Location: /teams/" . $_POST['team'] . "/manage?action=quali_signup&error=" . $error);
	} else {
		header("Location: /");
	}
	
} catch (Exception $e) {
	error_log("Caught Exception when processing quali signup: $e");
	header(isset($_POST['team']) ? "Location: /teams/".$_POST['team']. "/manage?error" : "/teams?error");
}


function validate($leader, $date, $team) {
	if ($date != '1' && $date != '2' && $date != 'none' && $date != 'both') {
		return 1;
	}

	if (!$leader->getInfo('registered') || !$leader->verified()) {
		return 2;
	}

	if (!$team->getInfo('exists') || !$team->isLeader($leader->getInfo('id'))) {
		return 3;
	}

	if (($team->getInfo('game') == 'cs' || 'lol') && count($team->getMembers()) < 5) {
		return 4;
	}
	if (($team->getInfo('game') == 'ow') && count($team->getMembers()) < 6) {
		return 4;
	}


	return 0;
}

?>
