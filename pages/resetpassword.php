<!--
 Copyright (C) 2018 Daniel Shields
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.
 
 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->

<?php
$page_name = 'Reset your password';

if (!isset($_GET['code']) || $_GET['code'] == "") {
	echo "Please provide a code";
	exit;
}

$code = $_GET['code'];

if (strlen($code) !== 128) {
	echo "Invalid code";
	exit;
}
?>

<h1 class="page-title">Password reset</h1>
<hr class="left"/>

<?php
if (isset($_GET['error']) && $_GET['error'] != 0) {
    echo "<div class='return-alert'>";
    switch ($_GET['error']) {
        case 2:
            ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">That code doesn't exist!</h4>
                <p>Please <a href="/discord" class="alert-link">get in touch</a> if this error persists.</p>
            </div>
            <?php
            break;
        case 3:
            ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Code expired or used!</h4>
                <p>Please <a href="/discord" class="alert-link">get in touch</a> if this error persists.</p>
            </div>
            <?php
            break;
        default:
            ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Something went wrong!</h4>
                <p>We're not exactly sure what though :(</p>
                <p>Please <a href="/discord" class="alert-link">get in touch</a> if this error persists.</p>
            </div>
            <?php
            break;
    }
  
  echo "</div>";
  
}
?>

<form id="registration-form" class="needs-validation" role="form" method="POST" action="api/password-reset.php">
<div class="form-row">

	<div class="col-md-6 mb-2">
		<label for="password">Password</label>
        <input autocomplete="new-password" name="password" type="password" class="form-control" id="password" placeholder="Password" required minlength="8" maxlength="128" oninput="checkPass(this)">
        <small id="emailHelp" class="form-text text-muted">Minimum length 8 characters.</small>
	</div>

	<div class="col-md-6 mb-2">
		<label for="password">Retype password</label>
        <input autocomplete="new-password" name="password" type="password" class="form-control" id="password-confirm" placeholder="Password" required minlength="8" oninput="checkPassConfirm(this)">

        <input type="hidden" name="code" value="<?=$code?>">

    </div>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>

  <script language='javascript' type='text/javascript'>
    var passwordConfirm = document.getElementById('password-confirm');

    function checkPass(input) {
        if (input.value != document.getElementById('password-confirm').value) {
            passwordConfirm.setCustomValidity('Passwords must match');
        } else {
            // input is valid -- reset the error message
            passwordConfirm.setCustomValidity('');
        }
    }

    function checkPassConfirm(input) {
        if (input.value != document.getElementById('password').value) {
            input.setCustomValidity('Passwords must match');
        } else {
            // input is valid -- reset the error message
            input.setCustomValidity('');
        }
    }
  </script>
</form>