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


if (!isset($_GET['code']) || $_GET['code'] == "") {
	echo "Please provide a code";
	exit;
}

$code = $_GET['code'];

if (strlen($code) !== 128) {
	echo "Invalid code";
	exit;
}

require_once '../includes/global_constants_variables.php';
require_once '../includes/db_config.php';
require_once '../includes/verification.php';
require_once '../includes/email.php';
require_once '../includes/user.class.php';

$stmt = DB::prepare("SELECT `userid`, `email`, `used`, `expiration` <= NOW() as `expired`  FROM `verifications` WHERE `hash`=?");
$stmt->execute([$code]);

$verification = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$verification) {
	header("Location: /");
	exit;
}

DB::beginTransaction();
try {
	if ($verification['used'] || $verification['expired']) {
		$user = User::fromID($verification['userid']);
		if ($user->verified()) {
			header("Location: /login");
			exit;
		}
	
		markVerificationUsed($verification['email']);
	
		if (sendVerification($verification['userid'], $verification['email'], $user->getInfo('first_name') . ' ' . $user->getInfo('last_name')) !== 202) {
			DB::rollBack();
			header("Location: /login?resenderror");
		} else {
			DB::commit();
			header("Location: /login?resendsuccess");
		}
		
		exit;
	}
	
	// flag email as verified
	$stmt = DB::prepare("UPDATE `emails` SET `verified`=1,`verified_time`=NOW() WHERE `email`=?");
	$stmt->execute([$verification['email']]);
	
	// flag verification code as used
	markVerificationUsed($verification['email']);
	
	DB::commit();
	header("Location: /login?verifysuccess");
} catch (Exception $e) {
	error_log("Caught Exception when verifying user: $e");
	DB::rollBack();
	
	if (PUBLISH_STATE == 'DEV') {
		echo $e->getMessage();
	} else {
		header("Location: /login?verifyerror");
	}
}



?>
