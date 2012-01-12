<?php

date_default_timezone_set('Europe/Belfast'); # Set your timezone. List of options: http://php.net/manual/en/timezones.php

$site->seperator = ' | '; # Used for page title
$site->title = 'Wee_ CMS'; # Used for page title

$site->author->name = 'David Turner'; # Your Name
$site->author->email = 'hi@davidturner.name'; # Contact Email Address
$site->author->twitter = ''; # Twitter Account Name

$site->analytics = ''; # Google Analytics Code
$site->theme = 'starkers'; # Site Theme.UA-16030018-1

$site->desc = ''; # Default Site Description
$site->tags = ''; # Default Site Tags

$site->posts->page = 5; # Posts Per Page
$site->posts->feed = 20; #Posts in RSS Feed

$site->cache->active = 0; # [1|0] 1 = cache site content

$site->closecomments = 0; # [1|0] 1 closes comments across the site
$site->comments = 'filtered'; # [filtered|all|none] all will save spam comments in as spam-file.md so they need to be approved

$site->copyright = '&copy; '.@date('Y').' '.$site->author->name; # meta copyright information - shouldn't need tweaked