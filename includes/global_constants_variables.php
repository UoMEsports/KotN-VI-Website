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

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);
date_default_timezone_set('UTC');

$env = getenv('KOTN_PUBLISH_STATE');
if ($env === false) {
    $publish_state = 'DEV';

    if(isset($_SERVER['SERVER_NAME'])) {
        switch ($_SERVER['HTTP_HOST']) {
            case 'uomesports.co.uk':
                $publish_state = 'LIVE';
                break;
            case 'beta.uomesports.co.uk':
                $publish_state = 'BETA';
                break;
        }
    }

} else {
    $publish_state = $env;
}

define('PUBLISH_STATE', $publish_state);

define('SESSION_NAME', 'uom');

// API keys
define('GOOGLE_API_KEY', 'REDACTED');
define('SENDINBLUE_API_KEY', 'REDACTED');

?>
