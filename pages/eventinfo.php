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
$page_name = 'Event Info';

include_once '../includes/global_constants_variables.php';
?>

<h1 class="page-title">The Event</h1>
<hr class='left'/>
<h2>10am-8pm</h2>
<h2>1st and 2nd December 2018</h2>
<h3><u>Venue</u></h3>
<ul>
    <li> University of Salford’s MediaCityUK campus, Salford, Manchester. </li>
    <li> It has excellent access via bus and Metrolink. </li>
    <li> For attendees, participants and sponsors travelling to the event from outside Manchester, it is advised that they take the Metrolink to Media City directly from Manchester Piccadilly Station. </li>
</ul>

<iframe class="venue-map" src="https://www.google.com/maps/embed/v1/place?q=University%20of%20Salford,MediaCityUK,%20UK
    &zoom=13
    &key=<?=GOOGLE_API_KEY?>">
</iframe>

<a target="_blank" class="btn btn-warning btn-lg" href="/tickets">Buy tickets here!</a>

<h2 class="info-header"><u>Activities - more to come!</u></h2>
<!--ACTIVITIES-->
<div class="container" id="activities">
  <div class="row">
    <div class="col-6">
      <div class="card activity">
        <div class="image-wrapper">
          <img class="card-img-top" src="<?=cacheBust('/assets/img/activities/cs_tournament.png')?>">
        </div>
        <div class="card-body">
        <h3 class="card-text">CS:GO University Championship Finals</h3>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card activity">
        <div class="image-wrapper">
          <img class="card-img-top" src="<?=cacheBust('/assets/img/activities/kotn-showdown.png')?>">
        </div>
        <div class="card-body">
          <h3 class="card-title">King of the North Showdown</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-6">
      <div class="card activity">
        <div class="image-wrapper">
          <img class="card-img-top" src="<?=cacheBust('/assets/img/activities/usl-2.png')?>">
        </div>
        <div class="card-body">
        <h3 class="card-text">University Siege League Finals</h3>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card activity">
        <div class="image-wrapper">
          <img class="card-img-top" src="<?=cacheBust('/assets/img/activities/society-stall.jpg')?>">
        </div>
        <div class="card-body">
          <h3 class="card-title">Society Stalls</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-6">
      <div class="card activity">
        <div class="image-wrapper">
          <img class="card-img-top" src="<?=cacheBust('/assets/img/activities/Bee-VR-logo.png')?>">
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card activity">
        <div class="card-body">
          <h3 class="card-title">Maker Space</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-6">
      <div class="card activity">
        <div class="card-body">
          <h3 class="card-title">Game Developers</h3>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card activity">
        <div class="card-body">
          <h3 class="card-title">Retro Games</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-6">
      <div class="card activity">
        <div class="card-body">
          <h3 class="card-title">Anthony Nolan</h3>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card activity">
        <div class="card-body">
          <h3 class="card-title">ROG Laptops</h3>
        </div>
      </div>
    </div>
  </div>
</div>

<h2 class="info-header"><u>Sponsors and Partners</u></h2>
<!--SPONSORS-->
<div class="container" id="sponsors">
  <div class="row">
    <div class="col-6">
      <div class="card sponsor">
      <div class="image-wrapper">
          <img class="card-img-top" src="<?=cacheBust('/assets/img/sponsors/nuel.jpg')?>">
        </div>
        <div class="card-body">
        <h3 class="card-title">The National University Esports League</h3>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card sponsor">
        <div class="image-wrapper">
          <img class="card-img-top" src="<?=cacheBust('/assets/img/sponsors/salford.jpg')?>">
        </div>
        <div class="card-body">
        <h3 class="card-text">University of Salford</h3>
        </div>
      </div>
    </div>
    <!--<div class="col-6">
      <div class="card sponsor">
        <div class="image-wrapper">
          <img class="card-img-top" src="/assets/img/sponsors/twitch.jpg">
        </div>
        <div class="card-body">
          <h3 class="card-title">Twitch</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-6">
      <div class="card sponsor">
      <div class="image-wrapper">
          <img class="card-img-top" src="/assets/img/sponsors/uom_su.jpg">
        </div>
        <div class="card-body">
          <h3 class="card-text">University of Manchester Students Union</h3>
        </div>
      </div>
    </div>
  </div>-->
</div>
