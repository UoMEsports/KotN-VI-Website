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

global $loggedInUser;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset=utf-8>
  <title><?= $html_title ?><?= isset($page_name) ? ' | ' . $page_name : '' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://use.typekit.net/mah1qnl.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="<?=cacheBust('/style.php?p=style.scss')?>">

  <!--Favicon-->
  <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/favicon/apple-touch-icon.png?v=5APKBwp2p7">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/favicon/favicon-32x32.png?v=5APKBwp2p7">
  <link rel="icon" type="image/png" sizes="16x16" href="/assets/img/favicon/favicon-16x16.png?v=5APKBwp2p7">
  <link rel="manifest" href="/assets/img/favicon/site.webmanifest?v=5APKBwp2p7">
  <link rel="mask-icon" href="/assets/img/favicon/safari-pinned-tab.svg?v=5APKBwp2p7" color="#5bbad5">
  <link rel="shortcut icon" href="/assets/img/favicon/favicon.ico?v=5APKBwp2p7">
  <meta name="msapplication-TileColor" content="#603cba">
  <meta name="msapplication-config" content="/assets/img/favicon/browserconfig.xml?v=5APKBwp2p7">
  <meta name="theme-color" content="#912c83">

  <!--Open Graph-->
  <meta property="og:title" content="<?= $html_title ?><?= isset($page_name) ? ' | ' . $page_name : '' ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://<?= $_SERVER['HTTP_HOST'] ?>" />
  <meta property="og:description" content="The UK's biggest student-run gaming festival!" />
  <meta property="og:image" content="https://<?= $_SERVER['HTTP_HOST']?>/assets/img/favicon/android-chrome-512x512.png" />
  <meta property="og:image:width" content="512" />
  <meta property="og:image:height" content="512" />
  <meta property="og:image:type" content="image/png" />
  <meta property="og:image:alt" content="King of the North VI Logo" />

</head>

<body>
  <div class="bg-overlay"></div>
  <div class="content">
  <div class="header">
    <img class="header-img" src="<?=cacheBust('/assets/img/header.png')?>"/>
    <a href="/"><img class="header-logo" src="<?=$page == 'kotnshowdown' || $page == 'userhome' || $page == 'register' || $page == 'teams' || $page == 'login' ? cacheBust('/assets/img/header-showdown.png') : cacheBust('/assets/img/header-logo.png')?>"/></a>

    <h1 style="display: none;">King of the North VI</h1>

    <h4 class="header-date">1<sup>st</sup> - 2<sup>nd</sup> December 2018</h4>

    <h4 class="header-location">MediaCityUK, Salford</h4>
  </div>

  <nav class="navbar navbar-expand">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="/eventinfo">The Event</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/kotnshowdown">KotN Showdown</a>
        </li>
      </ul>

      <ul class="navbar-nav ml-auto">
      
      <li class="nav-item">
        <a class="nav-link" href="/schedule">Schedule</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/faq">FAQ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/contact">Contact</a>
      </li>
      <!--
        <?php
        
        if (!!$loggedInUser) { ?>
    
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class='truncate'><?=$loggedInUser->getInfo('nick')?></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="/userhome">User Home</a>
              <a class="dropdown-item logout" href="?logout">Logout</a>
            </div>
          </li>

          <?php

          } else {
            ?><li class='nav-item'><a class='nav-link' href='/login'>Login/Register</a></li><?php
          }
  ?>
  -->
      </ul>
    </div>
  </nav>

  <div class="page">
  <?php
  $content = get_contents("content");

  // If content is filled, output it
  echo $content;

  ?>
  </div>

</div>


<div class="footer">
  <div class="footer-container">
    <div class="row">
      <div class="col-6 logos">
        <div class="logo">
          <img src="<?=cacheBust('/assets/img/sponsors/uom_white.png')?>">
        </div>
        <div class="logo">
          <img src="<?=cacheBust('/assets/img/sponsors/salford.png')?>">
        </div>
        <div class="logo">
          <img src="<?=cacheBust('/assets/img/sponsors/nuel_white.png')?>">
        </div>
        <!--<div class="logo">
          <img src="/assets/img/sponsors/twitch_white.png">
        </div>
        
        <div class="logo">
          <img src="/assets/img/sponsors/asus_rog.png">
        </div>-->
      </div>
      <div class="col-1 social"></div>
      <div class="col-5 credits">
        <p>King of the North © 2018</p>
        <p>Website by <a target="_blank" href="http://shieldsuk.me">Daniel Shields</a></p>
      </div>
    </div>
  </div>
</div>

  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="/assets/js/esports.js?9819198"></script>
</body>
</html>
