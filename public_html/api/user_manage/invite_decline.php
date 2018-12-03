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
	3: user has no pending invite from that team
	*/

	$error = -1;

	if (isset($_POST['team'], $_SESSION['userid']) && is_numeric($_POST['team'])) {
		require_once '../../../includes/db_config.php';
		require_once '../../../includes/user.class.php';

		$user = LoggedInUser::fromID($_SESSION['userid']);
		$teamid = $_POST['team'];

		if ($user->getInfo('registered')) {
			DB::beginTransaction();
			try {
				// set invite to declined
				$stmt = DB::prepare("UPDATE `user-invites` SET `declined`=1 WHERE `userid` = ? AND `teamid` = ?");
				$stmt->execute([$user->getInfo('id'), $teamid]);

				if ($stmt->rowCount() == 0) {
					$error = 3;
				} else {
					$error = 0;
				}

				DB::commit();
				
			} catch (Exception $e) {
				error_log("Caught Exception when declining invite: $e");
				DB::rollBack();
				$error = 4;
			}

		} else {
			// user not registered
			$error = 2;
		}

	} else {
		// invalid parameters
		$error = 1;
	}

	if (!$error) {
		header("Location: /userhome");
	} else {
		header("Location: /userhome?action=decline&error=". $error);
	}
} catch (Exception $e) {
	error_log("Caught Exception when declining invite: $e");

	header("Location: /userhome?error");
}