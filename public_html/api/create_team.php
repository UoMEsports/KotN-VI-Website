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
	require_once '../../includes/global_constants_variables.php';
	require 'post_base.php';

	session_start();

	/*Error codes:
	-1: unknown error
	0: success
	1: invalid parameters
	2: leader not registered/verified
	3: duplicate name
	4: user in team for game already
	5: invalid game
	6: team name invalid
	7: db error
	*/

	$error = -1;

	if (isset($_POST['team-name'], $_SESSION['userid'], $_POST['game'])) {
		require_once '../../includes/db_config.php';
		require_once '../../includes/user.class.php';

		$leader = LoggedInUser::fromID($_SESSION['userid']);

		if (!$leader->getInfo('registered')) {
			unset($_SESSION['userid']);
			header("Location: /" . $page);
		}
		
		$name = trim($_POST['team-name']);
		$game = trim($_POST['game']);
		
		$error = validate($leader, $name, $game);

		if ($error === 0) {
			DB::beginTransaction();
			try {
				// create team in `teams` table
				$stmt = DB::prepare("INSERT INTO `teams`(`game`, `name`, `uni`) VALUES (?, ?, ?)");
				$stmt->execute([$game, $name, $leader->getInfo('uni')]);

				// get teamid from last insert
				$teamid = DB::lastInsertId(); 
				
				// add leader to team
				$stmt = DB::prepare("INSERT INTO `user-teams`(`userid`, `teamid`, `leader`) VALUES (?, ?, 1)");
				$stmt->execute([$leader->getInfo('id'), $teamid]);

				DB::commit();
				$error = 0;
			} catch (Exception $e) {
				DB::rollBack();
				$errormsg = $e->getMessage();
				
				if (fnmatch("*Duplicate entry * for key 'name'*", $errormsg)) {
					$error = 3;
				} else {
					error_log("Caught Exception when creating team: $e");
					$error = 7;
				}

			}
		}
	} else {
		$error = 1;
	}

	if ($error === 0) {
		header("Location: /teams/" . $teamid);
	} else {
		header("Location: /teams/create?error=" . $error);
	}

} catch (Exception $e) {
	error_log("Caught Exception when creating team: $e");
	header('Location: /teams/create?error');
}


function validate($leader, $name, $game) {
	if (strlen($name) > 32 || preg_match('/^[a-zA-Z0-9 ]+$/', $name) !== 1) {
		return 6;
	}

	if (!$leader->getInfo('registered') || !$leader->verified()) {
		return 2;
	}

	if ($leader->hasTeamForGame($game)) {
		return 4;
	}

	if ($game != 'ow' && $game != 'lol') {
		return 5;
	}

	return 0;
	
}
?>
