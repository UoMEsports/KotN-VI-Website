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

<?php $page_name = 'Request a password reset'; ?>

<h1 class="page-title">Request password reset</h1>
<hr class="left"/>

<form id="passwordreset-form" class="needs-validation" role="form" method="POST" action="api/password-request.php">
  <div class="form-row">
	<div class="col-md-6 mb-2">
		<label for="email">Email address</label>
		<input autocomplete="email" name="email" type="email" class="form-control" id="email" placeholder="kotn@manchester.ac.uk" required>
	</div>
  </div>
  <button type="submit" class="btn btn-primary">Request Password Reset</button>
</form>