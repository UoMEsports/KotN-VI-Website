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
	require_once '../../includes/password_reset.php';

	session_start();

	/*Error codes:
	-1: unknown error
	0: success
	1: invalid parameters
	2: no such reset
	3: code expired/used
	4: database error
	*/

	$error = -1;

	if (isset($_POST['password']) && isset($_POST['code'])) {
		require_once '../../includes/db_config.php';
		
		$password = $_POST['password'];
		$code = $_POST['code'];

		// input invalid
		if (strlen($code) !== 128 || strlen($password) > 128 || strlen($password) < 8) {
			header("Location: /resetpassword?code=`$code`&error=1");
			exit;
		}
		
		$stmt = DB::prepare("SELECT `userid`, `email`, `used`, `expiration` <= NOW() as `expired`  FROM `password-resets` WHERE `hash`=?");
		$stmt->execute([$code]);

		$reset = $stmt->fetch(PDO::FETCH_ASSOC);

		// rest doesn't exist
		if (!$reset) {
			header("Location: /resetpassword?code=`$code`&error=2");
			exit;
		}

		// code invalid
		if ($reset['used'] || $reset['expired']) {
			header("Location: /resetpassword?code=`$code`&error=3");
			exit;
		}

		DB::beginTransaction();
		try {
			// reset password
			resetPassword($reset['userid'], $password);

			// success
			DB::commit();	
			header("Location: /login?passwordreset");
		} catch (Exception $e) {
			error_log("Caught Exception when processing password reset: $e");
			DB::rollBack();
			
			if (PUBLISH_STATE == 'DEV') {
				echo $e->getMessage();
			} else {
				header("Location: /resetpassword?code=`$code`&error=4");
				exit;
			}
		}

	} else {
		header("Location: /resetpassword?error=1");
	}

} catch (Exception $e) {
	error_log("Caught Exception when processing password reset: $e");
	header("Location: /resetpassword?error");
}
?>