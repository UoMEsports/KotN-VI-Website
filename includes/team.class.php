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


class Team {
	protected $_teamExists;
	protected $_teamData;
	protected $_teamMembers;
	protected $_teamUni;
	protected $_invites;

	public function __construct($teamid) {
		$this->update($teamid);
	}

	protected function update($teamid = null) {
		$teamid = isset($teamid) ? $teamid : $this->_teamData['id'];

		$stmt = DB::prepare("SELECT * FROM `teams` WHERE `id` = :teamid");
		$stmt->execute(['teamid' => $teamid]);
		$team = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->_teamExists = !!$team;

		if ($team) {
			$this->_teamData = $team;

			$this->fetchInvites();
			$this->fetchMembers();
		} else {
			$this->_teamData = false;
		}
	}

	private function fetchMembers() {
		// Fetch team members
		$stmt = DB::prepare("SELECT `userid` as `id` FROM `user-teams` WHERE `teamid`= ? ORDER BY `leader` DESC");
		$stmt->execute([$this->getInfo('id')]);
		$this->_teamMembers = $stmt->fetchAll() ?? false;
	}

	private function fetchInvites() {
		// Fetch user invites
		$stmt = DB::prepare("SELECT * FROM `user-invites` WHERE `teamid` = ?");
		$stmt->execute([$this->getInfo('id')]);
		$this->_invites = $stmt->fetchAll() ?? false;
	}

	// Forcefully remove member from the team
	public function kick($userid) {
		$stmt = DB::prepare("DELETE FROM `user-teams` WHERE `userid` = :userid AND `teamid` = :teamid");
		$result = $stmt->execute(['userid' => $userid, 'teamid' => $this->_teamData['id']]);

		$this->update();
		return $result;
	}

	// Identical to kick() for now
	public function leave($userid) {
		return kick($userid);
	}

	public function promote($userid) {
		$leaderid = $this->getLeader()['id'];

		$removeLeaderStmt = DB::prepare("UPDATE `user-teams` SET `leader` = 0 WHERE `userid`=:leaderid");
		$addLeaderStmt = "UPDATE `user-teams` SET `leader` = 1 WHERE `userid`=:promotionid";

		$removeResult = $removeLeaderStmt->execute(['leaderid' => $leaderid]);
		$addResult = $addLeaderStmt->execute(['promotionid' => $userid]);

		$this->update();
		return $removeResult && $addResult;
		
	}
	
	public function invite($userid) {
		$stmt = DB::prepare("INSERT INTO `user-invites`(`teamid`, `userid`) VALUES (:teamid, :userid)");
		$result = $stmt->execute(['teamid' => $this->getInfo('id'), 'userid' => $userid]);

		$this->update();
		return $result;
	}

	public function cancelInvite($inviteid) {
		$stmt = DB::prepare("UPDATE `user-invites` SET `cancelled`=1 WHERE `id`=?");
		$result = $stmt->execute([$inviteid]);

		$this->update();
		return $result;
	}

	// Delete whole team
	public function delete() {
		$kickSQL = "DELETE FROM `user-teams` WHERE `id` = :teamid";
		$deleteSQL = "DELETE FROM `teams` WHERE `team_id` = :teamid";

		$kickResult = $dbh->change($kickSQL, ['teamid' => $this->_id]);
		$deleteResult = $dbh->change($deleteSQL, ['teamid' => $this->_id]);

		$this->update();
		return $kickResult && $deleteResult;

	}

	/**********
	* GETTERS *
	***********/
	public function getInfo($info = false) {
		// all team data
		if ($info == false) {
			return $this->_teamData;
		}
		// team exist
		if ($info == 'exists') {
			return $this->_teamExists;
		}
		// rest of reachable data
		if (isset($this->_teamData[strtolower($info)])) {
			return $this->_teamData[strtolower($info)];
		}
		return false;
	}

	public function getMembers() {
		// ARRAY
		return $this->_teamMembers;
	}

	public function getLeader() {
		// INT: leader userid
		return $this->_teamMembers[0] ?? false;
	}

	public function isMember($userID) {
		// BOOL: is user in team?
		foreach ($this->_teamMembers as $teamMember) {
			if ($teamMember['id'] == $userID) {
				return true;
			}
		}
		return false;
	}

	public function isLeader($userID) {
		// BOOL: is user team leader?
		return $this->_teamMembers[0]['id'] === $userID;

	}

	public function getInvites($opt = 'all') {
		switch ($opt) {
			case 'active':
				$active_invites = [];
				foreach ($this->_invites as $invite) {
					if ($invite['declined'] == 0 && $invite['cancelled'] == 0 && $invite['accept_time'] == null) {
						$active_invites[] = $invite;
					}
				}
				return $active_invites;
				break;

			case 'cancelled':
				$cancelled_invites = [];
				foreach ($this->_invites as $invite) {
					if ($invite['cancelled'] == 1) {
						$cancelled_invites[] = $invite;
					}
				}
				return $cancelled_invites;
				break;
			
			case 'accepted':
				$accepted_invites = [];
				foreach ($this->_invites as $invite) {
					if ($invite['accept_time'] != null) {
						$accepted_invites[] = $invite;
					}
				}
				return $accepted_invites;
				break;

			case 'all':
				return $this->_invites;
				break;
		}
		return false;
	}

	public function getUni() {
		return $this->_teamUni;
	}

}

?>