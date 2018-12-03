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


class User {
	protected $_userRegistered;
	protected $_userData;
	protected $_teams;
	protected $_invites;
	protected $_email;

	public static function fromID($userid) {
		$instance = new static();
		$stmt = DB::prepare("SELECT * FROM `users` WHERE `id` = ?");
		$stmt->execute([$userid]);
		$userData = $stmt->fetch(PDO::FETCH_ASSOC);
		$instance->fillInfo($userData);
		return $instance;
	}

	protected function fillInfo($userData) {
		if ($userData) {
			$this->_userRegistered = true;
			$this->_userData = $userData;
		} else {
			$this->_userRegistered = false;
			$this->_userData = false;
		}
	}

	protected function fetchEmail() {
		$stmt = DB::prepare("SELECT * FROM `emails` WHERE `userid` = ?");
		$stmt->execute([$this->_userData['id']]);
		$this->_email = $stmt->fetch(PDO::FETCH_ASSOC) ?? false;
	}

	protected function fetchTeams() {
		$stmt = DB::prepare("SELECT `teamid` as `id`, `name`, `creation_time`, `leader`, `join_time`, `game`, `uni` FROM `teams` INNER JOIN `user-teams` ON `user-teams`.`teamid` = `teams`.`id` WHERE `userid` = ?");
		$stmt->execute([$this->getInfo('id')]);
		$this->_teams = $stmt->fetchAll() ?? false;
	}

	protected function fetchInvites() {
		$stmt = DB::prepare("SELECT `uni`, `teamid`, `name`, `creation_time`, `invite_time`, `accept_time`, `declined`, `cancelled`, `game` FROM `teams` INNER JOIN `user-invites` ON `user-invites`.`teamid` = `teams`.`id` WHERE `userid` = ? AND `declined` = 0 AND `cancelled` = 0 AND `accept_time` IS NULL");
		$stmt->execute([$this->getInfo('id')]);
		$this->_invites = $stmt->fetchAll() ?? false;
	}

	public function setReminded() {
		DB::exec("UPDATE `users` SET `reminded`=1 WHERE `id`=" . $this->getInfo('id'));
	}

	/********** 
	* GETTERS * 
	***********/ 
	public function getInfo($info = false) {
		// all user data
		if ($info == false) {
			return $this->_userData;
		}
		// user registered
		if ($info == "registered") {
			return $this->_userRegistered;
		}
		// rest of reachable data
		if (isset($this->_userData[strtolower($info)])) {
			return $this->_userData[strtolower($info)];
		}
		return false;
	}

	public function getEmail() {
		// fetch email if not already performed
		if (!isset($this->_email)) {
			$this->fetchEmail();
		}

		return $this->_email;
	}

	public function getTeams($game = null) {
		// fetch email if not already performed
		if (!isset($this->_teams)) {
			$this->fetchTeams();
		}
		if ($game) {
			foreach($this->_teams as $team) {
				if ($team['game'] == $game) {
					return $team;
				}
			}
			return false;
		}

		return $this->_teams;
	}

	public function verified() {
		// fetch email if not already performed
		if (!isset($this->_email)) {
			$this->fetchEmail();
		}

		return $this->_email != false && $this->_email['verified'] == 1;
	}
	
	public function getInvites() {
		// fetch invites if not already performed
		if (!isset($this->_invites)) {
			$this->fetchInvites();
		}

		return $this->_invites;
	}
	public function hasTeamForGame($game) {
		if (!isset($this->_teams)) {
			$this->fetchTeams();
		}
		if (empty($this->_teams)) {
			return false;
		}
		$result = false;
		foreach ($this->_teams as $team) {
			$result = $result || $team['game'] == $game;
		}

		return $result;
	}
	public function doesLeadTeam($teamid) {
		if (!isset($this->_teams)) {
			$this->fetchTeams();
		}

		$result = false;
		
		foreach ($this->_teams as $team) {
			$result = ($team['leader'] && ($team['id'] == $teamid)) || $result;
		}
		
		return $result;	
	}
}

class LoggedInUser extends User {
	protected function fillInfo($userData) {
		if ($userData) {
			$this->_userRegistered = true;
			$this->_userData = $userData;

			// Update profile if data is more than 2 minutes old
			if ((time() - strtotime($this->_userData['update_time'])) >= 120) {
				$this->update();
			}
		} else {
			$this->_userRegistered = false;
			$this->_userData = false;
		}
	}

	protected function update() {
		// only perform update if steam data is available
		if (isset($_SESSION['steamdata']['avatarmedium'])) {
			$ppUrl = shrinkPPURL($_SESSION['steamdata']['avatarmedium']);
			
			if (isset($ppUrl)) {
				$stmt = DB::prepare("UPDATE `users` SET `profile_pic`=? WHERE `id` = ?");
				$stmt->execute([$ppUrl, $this->_userData['id']]);
			}
		}
	}

	//GETTERS
}