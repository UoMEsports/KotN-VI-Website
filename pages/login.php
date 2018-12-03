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
$page_name = 'Login';


if (isset($_SESSION['userid'])) {
    header("Location: /userhome");
    exit;
}
?>
<h1 class="page-title">Login</h1>
<hr class="left"/>

<?php
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
    case 2:
        ?>
            <div class="alert alert-danger return-alert" role="alert">
                <h4 class="alert-heading">Incorrect details</h4>
                <p>Your email and password combination could not be found.</p>
                <p>Please check you typed your details correctly and try again.</p>
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
  
} elseif (isset($_GET['verify'])) {
    ?>
    <div class="alert alert-success return-alert" role="alert">
        <h4 class="alert-heading">Registration successful!</h4>
        <p>Please check your student email inbox and follow the link to verify your address.</p>
        <p>Please <a href="/discord" class="alert-link">get in touch</a> if you don't see it within an hour.</p>
        <p>The link will expire after 24 hours.</p>
    </div>
    <?php
} elseif (isset($_GET['verifysuccess'])) {
    ?>
    <div class="alert alert-success return-alert" role="alert">
        <h4 class="alert-heading">Email verification successful!</h4>
        <p>You may now login below.</p>
    </div>
    <?php
} elseif (isset($_GET['verifyerror'])) {
    ?>
    <div class="alert alert-danger return-alert" role="alert">
        <h4 class="alert-heading">We couldn't validate your email</h4>
        <p>Something went wrong when trying to validate your email.</p>
        <p>Please <a href="/discord" class="alert-link">get in touch</a> and we'll sort it out.</p>
    </div>
    <?php
} elseif (isset($_GET['resendsuccess'])) {
    ?>
    <div class="alert alert-warning return-alert" role="alert">
        <h4 class="alert-heading">That link has expired!</h4>
        <p>We have sent you a new verification link.</p>
    </div>
    <?php
} elseif (isset($_GET['resenderror'])) {
    ?>
    <div class="alert alert-danger return-alert" role="alert">
        <h4 class="alert-heading">That link has expired, but we couldn't send you a new one</h4>
        <p>Please <a href="/discord" class="alert-link">get in touch</a> and we'll sort it out.</p>
    </div>
    <?php
} elseif (isset($_GET['passwordrequest'])) {
    ?>
    <div class="alert alert-warning return-alert" role="alert">
        <h4 class="alert-heading">Please check your inbox</h4>
        <p>If the email you entered matched an account we've sent you a password reset link.</p>
        <p>If it doesn't arrive within 10 minutes, please <a href="/discord" class="alert-link">get in touch</a>.</p>
    </div>
    <?php
} elseif (isset($_GET['passwordreset'])) {
    ?>
    <div class="alert alert-success return-alert" role="alert">
        <h4 class="alert-heading">Password reset successful</h4>
        <p>You can now use your new password to login.</p>
    </div>
    <?php
}
?>

<div class="container">
  <div class="row">
    <div class="col-7">
      <form id="login-form" method="POST" action="api/login.php">
        <div class="form-group">
          <label for="email">Email address</label>
          <input name="email" type="email" class="form-control" id="email" placeholder="Enter email">
        </div>
        <div class="form-group">
          <label for="password">Password</label>
        <input name="password" type="password" class="form-control" id="password" placeholder="Password">
        <small id="passwordHelp" class="form-text text-muted"><a href='/requestpassword'>Forgotten your password?</a></small>
        </div>
        <button type="submit" class="btn btn-success">Login</button>
      </form>
    </div>
    <div class="col-5">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Not registered yet?</h5>
          <p class="card-text">Registering allows you to sign up to the KotN Showdown and join the qualifiers.</p>
          <a href="/register" class="btn btn-warning card-link">Register now</a>
        </div>
      </div>
    </div>
  </div>
</div>