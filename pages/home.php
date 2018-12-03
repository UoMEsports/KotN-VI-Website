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

<div class="container">
    <div class="row">
        <div id="twitch-embed" style="margin: auto; z-index: 50;"></div>
    </div>
    <div class="row">
        <div class="col-6">
            <h1>KotN Showdown</h1>
            <hr class="soft"/>
            <p>The largest student-run gaming festival returns for its sixth year!</p>
            <p>This year we're running our own tournaments in <b>League of Legends and Overwatch.</b></p>
            <p>If you win one of the two qualifiers held on the 27th/28th Oct and 3rd/4th Nov, you'll be invited to the grand finals at MediaCityUK on 1st or 2nd December 2018!</p>
            <p><b>These are free to enter!</b></p>
        </div>

        <div class="col-6">
            <h1>Exhibition</h1>
            <hr class="soft"/>
            <p>Saturday 1st and Sunday 2nd of December 2018 at University of Salford at MediaCityUK
            <p>Activities, giveaways and smaller tournaments to participate in. ANYONE - whether a student or not - can attend our live event and get involved.
            <p>Tickets are on sale now, and as this is a community event we are keeping ticket prices as low as we can: buy your ticket in advance for <b>£3, or get TEN tickets for £20!</b> - perfect for a group of friends or gaming communities.</p>
        </div>
    </div>
  <div class="row">
        <div class="col-6">
            <button class="btn btn-lg disabled" data-toggle='tooltip' data-placement='top' title='Qualifiers are over!'>Signup here!</button>
        </div>

        <div class="col-6">
        <a target="_blank" class="btn btn-warning btn-lg" href="/tickets">Buy tickets here!</a>
        </div>
    </div>
</div>

<div id="slideshow" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner" role="listbox">
        <div class="carousel-item active">
            <img class="d-block img-fluid" src="<?=cacheBust('/assets/img/slide/1.jpg')?>">
        </div>
        <div class="carousel-item">
            <img class="d-block img-fluid" src="<?=cacheBust('/assets/img/slide/2.jpg')?>">
        </div>
        <div class="carousel-item">
            <img class="d-block img-fluid" src="<?=cacheBust('/assets/img/slide/3.jpg')?>">
        </div>
        <div class="carousel-item">
            <img class="d-block img-fluid" src="<?=cacheBust('/assets/img/slide/4.jpg')?>">
        </div>
        <div class="carousel-item">
            <img class="d-block img-fluid" src="<?=cacheBust('/assets/img/slide/5.jpg')?>">
        </div>
        <div class="carousel-item">
            <img class="d-block img-fluid" src="<?=cacheBust('/assets/img/slide/6.jpg')?>">
        </div>
        <div class="carousel-item">
            <img class="d-block img-fluid" src="<?=cacheBust('/assets/img/slide/7.jpg')?>">
        </div>

    </div>
</div>

<script src="https://embed.twitch.tv/embed/v1.js"></script>
<script type="text/javascript">
    new Twitch.Embed("twitch-embed", {
        width: 760,
        height: 428,
        layout: "video",
        channel: "uomesports"
    });
</script>