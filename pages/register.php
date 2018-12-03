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
$page_name = 'Register';

if (isset($_SESSION['userid'])) {
	header("Location: /userhome");
	exit;
}
?>

<h1 class="page-title">Register</h1>

<h6>Registering allows you to sign up to the University tournament and join the qualifiers.</h6>
<hr class="left"/>

<?php
if (isset($_GET['error']) && $_GET['error'] != 0) {
    echo "<div class='return-alert'>";
    switch ($_GET['error']) {
        case 2:
        ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Verification email couldn't be sent!</h4>
                <p>Check your email was correctly typed then try again.</p>
                <p class="mb-0">If this issue persists, please let us know.</p>
            </div>
        <?php
            break;
        case 3:
        ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">That League Summoner Name is taken!</h4>
                <p>Please check you typed it right and try again.</p>
            </div>
        <?php
            break;
        case 4:
        ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">That Overwatch Battletag is taken!</h4>
                <p>Please check you typed it right and try again.</p>
            </div>
        <?php
            break;
            case 5:
        ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">An account is already registered to that email!</h4>
                <p>If the account is yours, you can login <a href="/login" class="alert-link">here</a>.</p>
            </div>
        <?php
            break;
        case 6:
        ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Something was incorrect.</h4>
                <p>Please check:</p>
                <ul>
                    <li>Your names are all less than 32 characters in length.
                    <li>Your names contain only alphabetic characters.
                    <li>Your email is less than 128 characters in length and a valid email.
                    <li>Your email is less than 128 characters in length.
                </ul>
            </div>
        <?php
            break;
        case 7:
        ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Your email is not academic!</h4>
                <p>Please register with an official UK university email that ends with ".ac.uk".</p>
            </div>
        <?php
            break;
        case 9:
        ?>
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">That username is taken!</h4>
                <p>Please sign up under a different name.</p>
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


<form id="registration-form" class="needs-validation" role="form" method="POST" action="api/register.php">
  <div class="form-row">
    <div class="col-md-4 mb-3">
      <label for="first_name">First name</label>
      <input name="first_name" type="text" class="form-control" id="first_name" placeholder="First name" required>
    </div>
    <div class="col-md-4 mb-3">
      <label for="last_name">Last name</label>
      <input name="last_name" type="text" class="form-control" id="last_name" placeholder="Last name" required>
    </div>
    <div class="col-md-4 mb-3">
      <label for="nick">Username</label>
      <input name="nick" type="text" class="form-control" id="nick" placeholder="Username" required>
    </div>
  </div>
  <div class="form-row">
    <div class="col-md-4 mb-2">
      <label for="overwatch">Overwatch Battletag</label>
      <div class="input-group">
        <input name="overwatch" type="text" class="form-control" id="overwatch" pattern="[A-Za-z0-9]+#[0-9]{4,5}$" title="Please enter a valid Battletag." placeholder="Name#1234" oninput="checkOverwatch(this)">
      </div>
    </div>
    <div class="col-md-4 mb-2">
      <label for="league">League Summoner Name</label>
      <div class="input-group">
        <input name="league" type="text" class="form-control" id="league" placeholder="Name1234" oninput="checkLeague(this)">
      </div>
    </div>
    <small id="ignHelp" class="form-text text-muted"><b>Just enter the IGNs for the game(s) you will be playing. If they need updating after registration, please <a href="/discord">let us know</a>.</b></small>
  </div>
  <div class="form-row">
	<div class="col-md-6 mb-2">
		<label for="email">Email address</label>
		<input autocomplete="email" name="email" type="email" class="form-control" id="email" placeholder="kotn@manchester.ac.uk" maxlength="128" required oninput="checkEmail(this)">
		<small id="emailHelp" class="form-text text-muted">This <b>must</b> be an email from an official UK university.</small>
	</div>
	<div class="col-md-6 mb-2">
    <label for="email-confirm">Retype email</label>
    <input name="email-confirm" type="email" class="form-control" id="email-confirm" placeholder="kotn@manchester.ac.uk" required maxlength="128" oninput="checkEmailConfirm(this)">
	</div>
  </div>
  <div class="form-row">
	<div class="col-md-6 mb-2">
		<label for="password">Password</label>
    <input autocomplete="new-password" name="password" type="password" class="form-control" id="password" placeholder="Password" required minlength="8" maxlength="128" oninput="checkPass(this)">
    <small id="passwordHelp" class="form-text text-muted">Minimum length 8 characters.</small>
	</div>
	<div class="col-md-6 mb-2">
		<label for="password">Retype password</label>
    <input autocomplete="new-password" name="password" type="password" class="form-control" id="password-confirm" placeholder="Password" required minlength="8" maxlength="128" oninput="checkPassConfirm(this)">
	</div>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>

  <script language='javascript' type='text/javascript'>
    var emailConfirm = document.getElementById('email-confirm');
    var passwordConfirm = document.getElementById('password-confirm');

    var overwatch = document.getElementById('overwatch');
    var league = document.getElementById('league');

    overwatch.setCustomValidity('At least one IGN required');

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

    function checkEmail(input) {
        if (input.value != document.getElementById('email-confirm').value) {
            emailConfirm.setCustomValidity('Emails must match');
        } else {
            // input is valid -- reset the error message
            emailConfirm.setCustomValidity('');
        }
    }

    function checkEmailConfirm(input) {
        if (input.value != document.getElementById('email').value) {
            input.setCustomValidity('Emails must match');
        } else {
            // input is valid -- reset the error message
            input.setCustomValidity('');
        }
    }

    function checkLeague(input) {
        if (input.value == '' && overwatch.value == '') {
            overwatch.setCustomValidity('At least one IGN required');
        } else {
            // input is valid -- reset the error message
            overwatch.setCustomValidity('');
        }
    }

    function checkOverwatch(input) {
        if (input.value == '' && league.value == '') {
            overwatch.setCustomValidity('At least one IGN required');
        } else {
            // input is valid -- reset the error message
            overwatch.setCustomValidity('');
        }
    }
  </script>
</form>