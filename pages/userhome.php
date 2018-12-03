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

<h1 class="page-title">User Home</h1>
<hr class='left'/>

<?php
global $loggedInUser;

// login if not already
if (!$loggedInUser) {
	header("Location: /login");
	exit;
}

if (isset($_GET['error'])) {
  switch ($_GET['error']) {
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
}

?>

<div class="container userpage">

<div class='row'>
	<h3 class='username'>Hello, <?=$loggedInUser->getInfo('first_name')?> <span style="font-weight: 600;"><?=$loggedInUser->getInfo('nick')?></span> <?=$loggedInUser->getInfo('last_name')?></h3>
</div>


<?php
// show only email management if no verified email found
if ($loggedInUser->verified()) {
	echo "<div class='row'>";

	echo "<div class='col-6'>";
	inviteMangement($loggedInUser);
	echo "</div>";

	echo "<div class='col-6'>";
	teamManagement($loggedInUser);
	echo "</div>";



	echo "</div>";
}

echo "<div class='row'>";
echo "<div class='col-12'>";
echo '<hr class="soft"/>';
emailManagement($loggedInUser);
echo '<hr class="soft"/>';
echo "</div>";
echo "</div>";

echo "<div class='row'>";
echo "<div class='col-2'></div>";
echo "<div class='col-8'>";
ignDisplay($loggedInUser);
echo "</div>";
echo "</div>";

echo "</div>";

function inviteMangement($loggedInUser) {
	$invites = $loggedInUser->getInvites();
	echo "<h4>Pending invites</h4><hr class='soft'/>";
	if ($invites) {
		echo "<ul class='invite-list'>";

		foreach ($invites as $invite) {

			?>
			<li>
				<i><a href="teams/<?=$invite['teamid']?>"><?=$invite['name']?></a></i>
				<form action='/api/user_manage/invite_accept.php' method='post'>
					<input type='hidden' name='team' value='<?=$invite['teamid']?>'/>
					<input class='btn btn-success' type='submit' value='Accept' <?=!$loggedInUser->hasTeamForGame($invite['game']) ? "" : " disabled data-toggle='tooltip' data-placement='top' title='You already have a team for this game.'"?>/>
				</form>
				<form action='/api/user_manage/invite_decline.php' method='post'>
					<input type='hidden' name='team' value='<?=$invite['teamid']?>'/>
					<input class='btn btn-danger' type='submit' value='Decline'/>
				</form>
			</li>
			<?php

		}
		echo "</ul>";
	} else {
		echo "<p>No pending invites.</p>";
	}
}

function teamManagement($loggedInUser) {
	$teams = $loggedInUser->getTeams();
	echo "<h4>Your teams</h4><hr class='soft'/>";
	if (!empty($teams)) {
		echo "<ul class='teams'>";
		foreach ($teams as $team) {
			echo "<li>";
			echo "<h4 style='display: inline;'><i><a href='teams/" . $team['id'] . "'>" . $team['name'] . "</a></i>   </h4>";

			if ($team['leader'] == 1) {
				echo "<a class='btn btn-primary' href='teams/" . $team['id'] . "/manage'>Manage Team</a>";
			} else {
				?>
				<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#areYouSureLeave<?=$team['id']?>">Leave</button>

				<!-- 'Are you sure?' Modal -->
				<div class="modal fade" id="areYouSureLeave<?=$team['id']?>" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="areYouSureLeaveLabel">Are you sure you want to leave this team?</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								You will have to be re-invited to join again.
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
								<form action='/api/user_manage/leave_team.php' method='post'>
									<input type='hidden' name='team' value='<?=$team['id']?>'/>
									<input class='btn btn-danger' type='submit' value='Leave'/>
								</form>
							</div>
						</div>
					</div>
				</div>
				<?php
			}

			echo "</li>";

		}

		echo "</ul>";
		
	} else {
		echo "<p>You are not in a team yet.</p>";
	}
	echo "<a style='display: block; width: auto;' class='btn btn-warning' href='/teams/create'>Create a Team</a>";
}

function emailManagement($loggedInUser) {
	$email = $loggedInUser->getEmail();
	if ($loggedInUser->verified()) {
		echo "<h5>Your verified email is <b>" . $email['email'] . "</b>.</h5>";
	} else {
		echo "<h5>Your email is <b>" . $email['email'] . "</b> but you have <b>not</b> verified it yet!</h5>";
		echo "<h6>Check your emails (and spam folder) for the link we sent you, but if nothing has come through please <a href='contact'>get in touch</a>.</h6>";
	}
}

function ignDisplay($loggedInUser) {
	$league = $loggedInUser->getInfo('league');
	$overwatch = $loggedInUser->getInfo('overwatch');

	echo '<h3>My IGNs</h3>';
	echo '<hr class="soft"/>';

	if ($league) {
		echo '<p>Your League of Legends Summoner Name is: <b>' . $league . '</b></p>';
	} else {
		echo "<p>You don't currently have a League of Legends Summoner Name</p>";
	}

	if ($overwatch) {
		echo '<p>Your Overwatch Battletag is: <b>' . $overwatch . '</b></p>';
	} else {
		echo "<p>You don't currently have an Overwatch Battletag</p>";
	}

	echo "<small>Please <a href='/discord'>contact an admin</a> if these need updating.</small>";
}
?>
