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


//Redirect to login page before sending email
//This makes it so the reset request form acts the same whether the email is legit or not
//This is to prevent reverse-lookups of emails
header("Location: /login?passwordrequest");

//Erase the output buffer
ob_end_clean();

//Tell the browser that the connection's closed
header("Connection: close");

//Ignore the user's abort (which we caused with the redirect).
ignore_user_abort(true);
//Extend time limit to 30 minutes
set_time_limit(1800);
//Extend memory limit to 10MB
ini_set("memory_limit","10M");
//Start output buffering again
ob_start();

//Tell the browser we're serious... there's really
//nothing else to receive from this page.
header("Content-Length: 0");

//Send the output buffer and turn output buffering off.
ob_end_flush();
flush();
//Close the session.
session_write_close();


require_once '../../includes/global_constants_variables.php';
require_once '../../includes/password_reset.php';

if (isset($_POST['email'])) {
	require_once '../../includes/db_config.php';
	
	$email = $_POST['email'];

	$stmt = DB::prepare("SELECT `userid` FROM `emails` WHERE `email` = ?");
	$stmt->execute([$email]);
	$userid = $stmt->fetch(PDO::FETCH_ASSOC)['userid'];

	$exists = isset($userid) && is_numeric($userid);

	DB::beginTransaction();

	try {
		if ($exists) {
			$stmt = DB::prepare("SELECT `first_name`, `last_name` FROM `users` WHERE `id` = ?");
			$stmt->execute([$userid]);
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

			$name = $user['first_name'] . ' ' . $user['last_name'];

			if (sendPasswordReset($userid, $email, $name) !== 202) {
				DB::rollBack();

				logFailedPasswordReset($email, 'EMAIL_NOT_SENT');
			} else {
				DB::commit();
			}
		} else {
			logFailedPasswordReset($email, 'NOT_FOUND');
			DB::commit();
		}
	} catch (Exception $e) {
		error_log("Caught Exception when processing password request: $e");
		DB::rollBack();
	}
}
?>