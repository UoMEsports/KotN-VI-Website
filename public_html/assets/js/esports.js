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

function jsUcfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// prevents spam of the function 'func' 
function debounce(func, interval) { 
	var lastCall = -1; 
	return function() { 
	  clearTimeout(lastCall); 
	  var args = arguments; 
	  var self = this; 
	  lastCall = setTimeout(function() { 
		func.apply(self, args); 
	  }, interval); 
	}; 
  } 

function addListeners() {
	$('#playersearch').bind("propertychange change keyup input paste", debounce(function(event) {
		var val = $(this).val();
		if (val != "") {
			searchPlayers(val, window.location.pathname.split('/')[2]);
		}
		
	}, 500));
	$(function () {
		$('[data-toggle="tooltip"]').tooltip()
	});
	
}

$(document).ready(function() {
	addListeners();
	
	var newUri = location.protocol + '//' + location.host + location.pathname;
	var code = findGetParameter('code');

	if (code) {
		newUri += '?code=' + code
	}

	console.log("Cleaned up URL from: " + window.location.href);
	window.history.replaceState({}, document.title, newUri);
});

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    location.search
        .substr(1)
        .split("&")
        .forEach(function (item) {
          tmp = item.split("=");
          if (tmp[0] === parameterName) result = tmp[1];
        });
    return result;
}

function displayUser(user) {
	var row = "<tr><td><h5>" + user['first_name'] + " <i>'" + user['nick'] + "'</i> " + user['last_name'] + "</h5></td>";
	row += "<td><form action='/api/team_manage/invite.php' method='POST'><input type='hidden' name='team' value='" + window.location.pathname.split('/')[2] + "'/><input type='hidden' name='user' value='" + user['id'] + "'/><input type='submit' value='Invite' class='btn btn-warning' href='#'/></td></tr>";
	
	$('#userresult').append(row);
}

function searchPlayers(search_term, team) {
	$.ajax({
        type: 'post',
		url:'/api/team_manage/user_lookup.php',
		data: {
			"search": search_term,
			"team": team
		},
        complete: function (response) {
			console.log(response);
			if (typeof response.responseJSON == 'undefined') {
				
			} else if (response.responseJSON.error !== '0') {
				var error = response.responseJSON.error;
				/*Error codes:
				-1: unknown error
				0: success
				1: invalid parameters
				*/

			} else {

				// handle successful acceptance
				$('#userresult').empty();

				var results = response.responseJSON.results;

				if (results.length == 0) {
					var row = "<tr><td><h5>No users found</h5></td>";
					$('#userresult').append(row);
				}

				for (i in results) {
					displayUser(results[i]);
				}
			}

        },
        error: function () {
			// display generic error
		}
	});
	return false;
}
