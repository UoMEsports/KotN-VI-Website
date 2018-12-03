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
	require '../../includes/db_config.php';

	session_start();

	/*Error codes:
	-1: unknown error
	0: success
	1: invalid parameters
	2: verification email failed
	3: duplicate league ign
	4: duplicate overwatch ign
	5: duplicate email
	6: validation error
	7: email is not academic
	8: database error
	9: duplicate nick
	*/

	$error = -1;

	if (isset($_POST['nick'], $_POST['league'], $_POST['overwatch'], $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['password'])) {
		require_once '../../includes/user.class.php';
		require_once '../../includes/verification.php';
		require_once '../../includes/email.php';

		$league = trim($_POST['league']);
		$overwatch = trim($_POST['overwatch']);

		$league = $league == '' ? NULL : $league;
		$overwatch = $overwatch == '' ? NULL : $overwatch;

		$nick = trim($_POST['nick']);
		$first_name = trim($_POST['first_name']);
		$last_name = trim($_POST['last_name']);
		$email = strtolower(trim($_POST['email']));
		$password = $_POST['password'];

		$error = validate($first_name, $nick, $last_name, $email, $password);

		if ($error === 0) {
			DB::beginTransaction();
			try {
				// check if the uni already exists
				$unis = DB::query("SELECT * FROM `unis`");
				$user_uni = false;

				foreach ($unis as $uni) {
					$ending = $uni['website'];

					$length = strlen($ending);
					
					if (substr($email, -$length) === $ending) {
						$user_uni = $uni;
						break;
					}
				}

				// create the uni if it doesn't
				if (!$user_uni) {
					$ending = implode('.', array_slice(explode('.', $email), -3));

					$stmt = DB::prepare("INSERT INTO `unis`(`website`) VALUES (?)");
					$stmt->execute([$ending]);

					$uniid = DB::lastInsertId();
				} else {
					$uniid = $uni['id'];
				}
				
				// create user in `users` table
				$stmt = DB::prepare("INSERT INTO `users`(`nick`, `first_name`, `last_name`, `league`, `overwatch`, `uni`) VALUES (:nick, :first_name, :last_name, :league, :overwatch, :uni)");
				$stmt->execute(['nick' => $nick, 'first_name' => $first_name, 'last_name' => $last_name, 'uni' => $uniid, 'league' => $league, 'overwatch' => $overwatch]);

				// get userid from last insert
				$userid = DB::lastInsertId();
				
				// add user email to db
				$stmt = DB::prepare("INSERT INTO `emails`(`userid`, `email`) VALUES (:userid, :email)");
				$stmt->execute(['userid' => $userid, 'email' => $email]);

				$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

				// add user password to db
				$stmt = DB::prepare("INSERT INTO `passwords`(`userid`, `hash`, `add_date`) VALUES (:userid, :hash, CURDATE())");
				$stmt->execute(['userid' => $userid, 'hash' => $hash]);

				// create_code must be the last action before DB::commit() as an email will be sent if it succeeds, even if subsequent code fails
				if ((sendVerification($userid, $email, $first_name . ' ' . $last_name)) !== 202) {
					// verification failed to send
					DB::rollBack();
					$error = 2;
				} else {
					// everything succeeded
					DB::commit();
					$error = 0;
				}
			} catch (Exception $e) {
				DB::rollBack();
				$errormsg = $e->getMessage();
				
				if (fnmatch("*Duplicate entry * for key 'nick'*", $errormsg)) {
					$error = 9;
				} else if (fnmatch("*Duplicate entry * for key 'league'*", $errormsg)) {
					$error = 3;
				} elseif (fnmatch("*Duplicate entry * for key 'overwatch'*", $errormsg)) {
					$error = 4;
				} elseif (fnmatch("*Duplicate entry * for key 'email'*", $errormsg) || fnmatch("*Duplicate entry '" . $email . "'*", $errormsg)) {
					$error = 5;
				} else {
					error_log("Caught Exception when processing registration: $e");
					if (PUBLISH_STATE == 'DEV') {
						echo $errormsg;
					}
					$error = 8;
				}

			}
		}
	} else {
		$error = 1;
	}

	if ($error === 0) {
		header("Location: /login?verify");
	} else {
		if (PUBLISH_STATE == 'DEV') {
			echo "<br>error = " . $error;
			die();
		}
		header("Location: /register?error=" . $error);
	}
} catch (Exception $e) {
	error_log("Caught Exception when processing registration: $e");
	header("Location: /register?error");
}

function validate($first_name, $nick, $last_name, $email, $password) {
	$valid = true;

	// length validation
	$valid = $valid &&
			 	strlen($nick) 			<=	32 &&
				strlen($first_name) 	<=	32 &&
				strlen($last_name) 		<=	32 &&
				strlen($email) 			<=	128 &&
				strlen($password)       >= 8 && strlen($password) <= 128;
				
	// names validation
	$valid = $valid && (preg_match('/^[a-zA-Z ]+$/', $first_name) === 1);
	
	$valid = $valid && (preg_match('/^[a-zA-Z ]+$/', $last_name) === 1);
	$valid = $valid && (preg_match('/^[a-zA-Z0-9 !@#\$%\^\&*\)\(+=._-]+$/', $nick) === 1);
	
	// global email validation
	$valid = $valid && (preg_match('/[^\s@]+@[^\s@]+\.[^\s@]+/', $email) === 1);

	if (!$valid) {
		return 6;
	}
	
	// uni email validation
	
	if (!(substr($email, -strlen('.ac.uk')) === '.ac.uk')) {
		return 7;
	}

	return 0;
}
?>
