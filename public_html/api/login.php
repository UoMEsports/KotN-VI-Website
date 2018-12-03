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
	require_once 'post_base.php';

	session_start();

	/*Error codes:
	-1: unknown error
	0: success
	1: invalid parameters
	2: incorrect details

	*/

	$error = -1;

	if (isset($_POST['email'], $_POST['password'])) {
		require_once '../../includes/db_config.php';
		
		$email = $_POST['email'];
		$password = $_POST['password'];

		$stmt = DB::prepare("SELECT `userid` FROM `emails` WHERE `email` = ?");
		$stmt->execute([$email]);
		$userid = $stmt->fetch(PDO::FETCH_ASSOC)['userid'];

		if (isset($userid) && is_numeric($userid)) {

			$stmt = DB::prepare("SELECT `hash` FROM `passwords` WHERE `userid` = ?");
			$stmt->execute([$userid]);

			$hash = $stmt->fetch(PDO::FETCH_ASSOC)['hash'];

			$correct = password_verify($password, $hash);
			
			if ($correct === TRUE) {
				$_SESSION['userid'] = $userid;

				$error = 0;
			} else {
				$error = 2;
				unset($_SESSION['userid']);
			}
		} else {
			$error = 2;
		}
		
	} else {
		// invalid parameters
		$error = 1;
	}

	if ($error) {
		if (PUBLISH_STATE == 'DEV') {
			echo "error: " . $error;
		} else {
			header("Location: /login?error=". $error);
		}
	} else {
		header("Location: /userhome");
	}
} catch (Exception $e) {
	error_log("Caught Exception when processing login: $e");
	if (PUBLISH_STATE == 'DEV') {
		echo $e->getMessage();
	} else {
		header("Location: /login?error");
	}
}
?>