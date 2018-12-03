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
global $url_parts, $loggedInUser;
require "../includes/team.class.php";

// is a teamid specified?
if (isset($url_parts[1]) && is_numeric($url_parts[1])) {
	$team = new Team($url_parts[1]);

	?>
	<h1 class="page-title">Teams</h1>
	<hr class="left"/>
	<?php

	if ($team->getInfo('exists')) {
		echo "<h1>" . $team->getInfo('name') . " - <img class='team-game' src='/assets/img/games/" . $team->getInfo('game') . ".png'/></h1>";
		echo "<div class='container teampage'>";
		// should we show the management page for this team?
		if (isset($url_parts[2]) && $url_parts[2] == 'manage') {
			manageTeam($loggedInUser, $team);
		} else {
			showTeam($team);
		}

		echo "</div>";
	} else {
		echo "<h1>Oops! We can't find that team :( </h1>";
	}

// the non-specific-team routes are left

} elseif (isset($url_parts[1]) && $url_parts[1] == 'create') {
	createTeam($loggedInUser);
} else {
	allTeams();
}

function allTeams() {
	$teams = DB::query("SELECT `teams`.`id`, `teams`.`name`, `unis`.`name` as `uni` FROM `teams` INNER JOIN `unis` ON `teams`.`uni` = `unis`.`id`");
	$page_name = 'Teams';
	
	?>
	<h1 class="page-title">Teams</h1>
	<hr class="left"/>

	<table class='team-list'>
		<tr>
			<th>Uni</th>
			<th>Name</th>
		</tr>
		<?php

		foreach ($teams as $team) {
			echo "<tr>";

			echo "<td>";
			echo $team['uni'];
			echo "</td>";

			echo "<td><a href='/teams/" . $team['id'] . "'>";
			echo $team['name'];
			echo "</a></td>";

			echo "</tr>";
		}
	?>
	</table>
<?php
}

function showTeam($team) {
	global $loggedInUser;

	if ($loggedInUser && $loggedInUser->verified() && $team->isLeader($loggedInUser->getInfo('id'))) {
		header("Location: /teams/". $team->getInfo('id') . "/manage");
	}

	$page_name = $team->getInfo('name');

	$game = $team->getInfo('game') == 'lol' ? 'league' : 'overwatch';

	?>
	
	<div class='row'>
		<div class='col-6'>
		<h4>Members</h4>
		<hr class='soft'/>
		<ul>
	<?php

	foreach ($team->getMembers() as $memberID) {
		$member = User::fromID($memberID['id']);

		echo "<li>";
		echo $member->getInfo('first_name') . " <i>'" . $member->getInfo($game) . "'</i> " . $member->getInfo('last_name');
		echo "</li>";
	}

	?>
	</ul>
	</div>
	<div class='col-6'>


	

	</div>
	</div>
	<?php
}

function manageTeam($loggedInUser, $team) {
	if (!($loggedInUser && $loggedInUser->verified() && $team->isLeader($loggedInUser->getInfo('id')) )) {
		header("Location: /teams/" . $team->getInfo('id'));
		exit;
	}

	$page_name = $team->getInfo('name') . 'Management';

	if (isset($_GET['error'])) {
		?>
			<div class="alert alert-danger" role="alert">
			<h4 class="alert-heading">Something went wrong</h4>
			<p>Unfortunately we're not exactly sure what :(</p>
			<p>Please <a href="/discord" class="alert-link">get in touch</a> if this error persists.</p>
			</div>
		<?php
	}

	?>
	<div class="row">
		<div class="col-6">
		<h4>Team Members</h4><hr class='soft'/>
	<table>
		<?php

	foreach ($team->getMembers() as $memberID) {
		$member = User::fromID($memberID['id']);

		echo "<tr>";

		echo "<td>";
		echo $member->getInfo('first_name') . " <i>'" . $member->getInfo('nick') . "'</i> " . $member->getInfo('last_name');
		echo "</td>";

		if ($member->getInfo('id') !== $team->getLeader()['id']) {
			?>
			<td>
			
			<form action='/api/team_manage/kick.php' method='post'>
				<input type='hidden' name='user' value='<?=$member->getInfo('id')?>'/>
				<input type='hidden' name='team' value='<?=$team->getInfo('id')?>'/>
				<input class='btn btn-danger' type='submit' value='Kick'/>
			</form>

			</td>
			
			<?php
			//echo "<td>";
			//echo "<button class='promote-button' onclick='promoteMember(" . $memberID['id'] .  ")'>Promote to Leader</button>";
			//echo "</td>";
		}

		echo "</tr>";
	}
	
	echo '</table>';
	echo "</div>";
	echo "<div class='col-6'>";

	echo "<h4>Pending invites</h4><hr class='soft'/>";

	if (count($team->getInvites('active')) == 0) {
		echo "<p>No outgoing invites.</p>";
	} else {
		?>
		<table>
		<?php

		foreach ($team->getInvites('active') as $invite) {
			$user = User::fromID($invite['userid']);

			echo "<tr>";
			echo "<td>";
			echo $user->getInfo('first_name') . " <i>'" . $user->getInfo('nick') . "'</i> " . $user->getInfo('last_name');
			echo "</td>";

			echo "<td>";
			?>
			<form action='/api/team_manage/invite_cancel.php' method='post'>
				<input type='hidden' name='team' value='<?=$invite['teamid']?>'/>
				<input type='hidden' name='invite' value='<?=$invite['id']?>'/>
				<input class='btn btn-danger' type='submit' value='Cancel'/>
			</form>
			</td>
			</tr>
			<?php
		}

		echo "</table>";
	}

	?>

		</div>
	</div>
	<div class='row'>
		<div class="col-4">
			<form>
			<div class="form-group">
				<label for="playersearch">Send an invite</label>
				<input class="form-control" type="text" id="playersearch" placeholder="e.g. 'Dr. Poo'"/>
				<small id="searchHelp" class="form-text text-muted">Start typing a nickname...</small>
			</div>
			</form>
		</div>
		<div class="col-8">
			<table>
				<tbody id="userresult">

				</tbody>
			</table>
		</div>
	</div>
	<div class='row'>
		<?php
		switch ($team->getInfo('qual')) {
			case 'none':
				if (($team->getInfo('game') == 'lol' && count($team->getMembers()) < 5) || ($team->getInfo('game') == 'ow' && count($team->getMembers()) < 6)) {
					?>
					<div class="alert alert-danger quali-signup" role="alert">
						<h4 class="alert-heading">You currently can't signup to a qualifier, because you don't have enough members for a full team</h4>
					</div>

					<?php
				} else {
					?>
					<div class="alert alert-danger quali-signup" role="alert">
						<h4 class="alert-heading">You currently aren't signed up to a qualifier</h4>
						<form class='needs-validation' action='/api/team_manage/quali_signup.php' method='post'>
							<input type='hidden' name='team' value='<?=$team->getInfo("id")?>'>
							<div class="form-group">
								<select name='date' class="form-control" id="dateSelect" required>
									<option value=''>Please select a date...</option>

									<option value='1'><?=$team->getInfo('game') == 'lol' ? '27th' : '28th' ?> October</option>
									<option value='2'><?=$team->getInfo('game') == 'lol' ? '3rd' : '4th' ?> November</option>
									
									<option value='both'>Both Dates</option>
								</select>
							</div>
							<div class="form-check">
								<input type="checkbox" name="confirm" class="form-check-input" id="confirm" required>
								<label class="form-check-label" for="confirm">By signing up to qualifiers, I acknowledge I have read and agree with the rules for my game and will <b>attend LAN if my team wins</b></label>
							</div>
							<button class="btn btn-primary" type="submit">Submit</button>
						</form>
					</div>

					<?php
				}
				break;
			case '1':
				?>
				<div class="alert alert-success quali-signup" role="alert">
					<h4 class="alert-heading">You're currently registered to play in the 1st qualifier on the <b><?=$team->getInfo('game') == 'lol' ? '27th October' : '28th October' ?></b>.</h4>
					<form class='needs-validation' action='/api/team_manage/quali_signup.php' method='post'>
						<input type='hidden' name='team' value='<?=$team->getInfo("id")?>'>
						<div class="form-group">
							<label for="date">Please select a new date below.</label>
							<select name='date' class="form-control" id="dateSelect" required>
								<option>Please select a date...</option>

								<option value='2'><?=$team->getInfo('game') == 'lol' ? '3rd' : '4th' ?> November</option>

								<option value='both'>Both Dates</option>
								<option value='none'>Drop out</option>
							</select>
						</div>
						<button class="btn btn-primary" type="submit">Submit</button>
					</form>
				</div>

				<?php
				break;

			case '2':
				?>
				<div class="alert alert-success quali-signup" role="alert">
					<h4 class="alert-heading">You're currently registered to play in the 2nd qualifier on the <b><?=$team->getInfo('game') == 'lol' ? '3rd November' : '4th November' ?></b>.</h4>
					<form class='needs-validation' action='/api/team_manage/quali_signup.php' method='post'>
						<input type='hidden' name='team' value='<?=$team->getInfo("id")?>'>
						<div class="form-group">
							<label for="date">You may change this here.</label>
							<select name='date' class="form-control" id="dateSelect" required>
								<option>Please select a date...</option>

								<option value='1'><?=$team->getInfo('game') == 'lol' ? '27th' : '28th' ?> October</option>

								<option value='both'>Both Dates</option>
								<option value='none'>Drop out</option>
							</select>
						</div>
						<button class="btn btn-primary" type="submit">Submit</button>
					</form>
				</div>

				<?php
				break;

			case 'both':
				?>
				<div class="alert alert-success quali-signup" role="alert">
					<h4 class="alert-heading">You're currently registered to play in both the <b><?=$team->getInfo('game') == 'lol' ? '27th October' : '28th October' ?></b> and the <b><?=$team->getInfo('game') == 'lol' ? '3rd November' : '4th November' ?></b> qualifier.</h4>
					<form class='needs-validation' action='/api/team_manage/quali_signup.php' method='post'>
						<input type='hidden' name='team' value='<?=$team->getInfo("id")?>'>
						<div class="form-group">
							<label for="date">You may change this here.</label>
							<select name='date' class="form-control" id="dateSelect" required>
								<option>Please select a date...</option>

								<option value='1'><?=$team->getInfo('game') == 'lol' ? '27th' : '28th' ?> October</option>
								<option value='2'><?=$team->getInfo('game') == 'lol' ? '3rd' : '4th' ?> November</option>

								<option value='none'>Drop out</option>
							</select>
						</div>
						<button class="btn btn-primary" type="submit">Submit</button>
					</form>
				</div>

				<?php
				break;
		}

		?>
	</div>
	<?php

}

function createTeam($loggedInUser) {
	echo '<h1 class="page-title">Create a team</h1>
	<hr class="left"/>';

	$page_name = 'Create a team';

	if (isset($_GET['error']) && $_GET['error'] != 0) {
		switch ($_GET['error']) {
			case 2:
				?>
				<div class="alert alert-danger" role="alert">
					<h4 class="alert-heading">You are not allowed to create a team.</h4>
					<p>Please first verify your email address then try again.</p>
				</div>
				<?php
				break;
			case 3:
				?>
				<div class="alert alert-danger" role="alert">
					<h4 class="alert-heading">That team name is taken!</h4>
					<p>Please choose a different name.</p>
					</div>
				<?php
				break;
			case 4:
				?>
				<div class="alert alert-danger" role="alert">
					<h4 class="alert-heading">Could not create team.</h4>
					<p>You are already a member of a team playing the same game!</p>
					<p>Delete or leave that team and try again.</p>
					</div>
				<?php
				break;
			case 6:
				?>
				<div class="alert alert-danger" role="alert">
					<h4 class="alert-heading">Could not create team.</h4>
					<p>Team name can only contain alphnumeric characters or spaces.</p>
					</div>
				<?php
				break;
			default:
				?>
				  <div class="alert alert-danger" role="alert">
					<h4 class="alert-heading">Could not create team</h4>
					<p>Something went wrong, but we're not exactly sure what :(</p>
					<p>Please <a href="/discord" class="alert-link">get in touch</a> if this error persists.</p>
				  </div>
				<?php
				break;
		}
	}

	if (!$loggedInUser || !$loggedInUser->getInfo('registered') || !$loggedInUser->verified()) {
		header("Location: /register");
	} else {
		?>
		<form id="team-create-form" class="needs-validation" role="form" method="POST" action="/api/create_team.php">
			<div class="form-group">
			<label for="team-name">Team Name</label>
			<input type="text" class="form-control" id="team-name" name="team-name" placeholder="University of Manchester" required>
			<small id="nameHelp" class="form-text text-muted"><b>Use of offensive/inappropriate team names will result in your team being deleted.</b></small>
			</div>
			<div class="form-group">
			<label for="game">Select Game</label>
			<select class="form-control" id="game" name="game" required>
				<option value=''>---</option>
				<option value='lol'>League of Legends</option>
				<option value='ow'>Overwatch</option>
			</select>
			</div>
			<button type="submit" class="btn btn-primary">Submit</button>
		</form>
		<?php
	}
}