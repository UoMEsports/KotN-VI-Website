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

// Generate a new verification code and send it to the user
function sendVerification($userid, $email, $name) {

	$hash = hash("sha512", $email . time() . rand());

	$verification = [$userid, $email, $hash];

	$stmt = DB::prepare("INSERT INTO `verifications`(`userid`, `email`, `hash`, `expiration`) VALUES (?, ?, ?, NOW() + INTERVAL 24 HOUR)");

	// WARNING: ensure you have correct transactional setups in the call location or this could fail and not be handled correctly
	$stmt->execute($verification);

	$request_body = new \SendinBlue\Client\Model\SendSmtpEmail([
		'sender' => ['name' => 'King of the North VI', 'email' => 'kotn@uomesports.co.uk'],
		'to' => [['name' => $name, 'email' => $email]],
		'htmlContent' => "Please click <a href='https://uomesports.co.uk/verify.php?code=$hash'>here</a> to verify your email address.<br><br>Alternatively copy and paste the link below into your browser:<br> https://uomesports.co.uk/verify.php?code=$hash",
		'subject' => 'Please verify your student email'
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
		error_log("Caught Exception when sending verification email: $e");
		return 500;
	}
}

// Mark a verification code as used
function markVerificationUsed($email) {
	$stmt = DB::prepare("UPDATE `verifications` SET `used`=1 WHERE `email`=?");
	$stmt->execute([$email]);

	return;
}