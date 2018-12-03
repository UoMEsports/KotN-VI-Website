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


require_once(__DIR__ . '/../vendor/autoload.php');

// Generate a new password reset code and send it to the user
function sendPasswordReset($userid, $email, $name) {

	$stmt = DB::prepare("SELECT `userid` FROM `emails` WHERE `email` = ?");
	$stmt->execute([$email]);
	$userid = $stmt->fetch(PDO::FETCH_ASSOC)['userid'];

	$hash = hash("sha512", $email . time() . rand());

	$reset = [$userid, $email, $hash];

	$stmt = DB::prepare("INSERT INTO `password-resets`(`userid`, `email`, `hash`, `expiration`) VALUES (?, ?, ?, NOW() + INTERVAL 1 HOUR)");

	// WARNING: ensure you have correct transactional setups in the call location or this could fail and not be handled correctly
	$stmt->execute($reset);

	$request_body = new \SendinBlue\Client\Model\SendSmtpEmail([
		'sender' => ['name' => 'King of the North VI', 'email' => 'kotn@uomesports.co.uk'],
		'to' => [['name' => $name, 'email' => $email]],
		'htmlContent' => "<p>Please click <a href='https://uomesports.co.uk/resetpassword?code=$hash'>here</a> to change it.</p>
			<p>Alternatively copy and paste the link below into your browser:<br> https://uomesports.co.uk/resetpassword?code=$hash</p>
			<p>The code will expire after 1 hour.</p>",
		'subject' => 'New password reset request'
	]);

	$config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', SENDINBLUE_API_KEY);
	
	$apiInstance = new SendinBlue\Client\Api\SMTPApi(
		new GuzzleHttp\Client(),
		$config
	);
	
	try {
		$result = $apiInstance->sendTransacEmail($request_body);
		// this is hard coded :/
		return 202;
	} catch (Exception $e) {
		error_log("Caught Exception when sending password reset email: $e");
		return 500;
	}
}

function logFailedPasswordReset($email, $reason) {
	$stmt = DB::prepare("INSERT INTO `password-reset-fails`(`email`, `reason`) VALUES (?, ?)");

	// WARNING: ensure you have correct transactional setups in the call location or this could fail and not be handled correctly
	$stmt->execute([$email, $reason]);
}

function resetPassword($userid, $password) {
	$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

	// WARNING: ensure you have correct transactional setups in the call location or this could fail and not be handled correctly

	// delete old password
	$stmt = DB::prepare("DELETE FROM `passwords` WHERE `userid` = ?");
	$stmt->execute([$userid]);

	// add new password to db
	$stmt = DB::prepare("INSERT INTO `passwords`(`userid`, `hash`, `add_date`) VALUES (:userid, :hash, CURDATE())");
	$stmt->execute(['userid' => $userid, 'hash' => $hash]);

	// mark reset code as used
	$stmt = DB::prepare("UPDATE `password-resets` SET `used`=1 WHERE `userid`=?");
	$stmt->execute([$userid]);
}
