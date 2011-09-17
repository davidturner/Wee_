<?php

# Pick one of these, used for defining whether site urls should have www. at the start or not.
//$url = 'http://'.$_SERVER['SERVER_NAME'];//If you don't want to replace www. in urls
$url = 'http://'.str_replace('www.', '', $_SERVER['SERVER_NAME']);

# Site Information used in various parts of the CMS.
define("SiteName", "Portfolio of David Turner");  // Used in RSS feed, can be used for Page Title generation
define("Desc","Thoughts, musings and experimentations of David Turner");  // Used to set the RSS Feed's Description
define("Author", "David Turner", true);  // Sets default post Author. Can be overridden on individual posts
define("siteEmail","hi@davidturner.name");

# Some Site Data
define("Divider", " | ");  // Separator, used for page title generation and nothing else
define("url", $url);  // Defining home address, used in a few places for making URLS work everywhere

# Post totals for category page and for RSS feeds
define("PostsPerPage", 5);  // Posts per category page
define("FeedTotal", 20);  // Posts listed in the site's RSS feed.

# Default <meta> information for site, only used in very limited circumstances
define("SiteDescription","");
define("SiteKeywords","");

# Settings that relate to content of site and RSS Feed
define("theme", "starkers");  // Setting theme for site, can be created/tweaked in /-template/themename/
define("nofeed", ".,..,.DS_Store,pages,demos,code"); // Sections you DON'T want showing up in your site's RSS
define("nocomments","pages,examples");  //  Pages I don't want comments available on
define("cache",false);  // Setting site caching to either true or false. If true, the /cache/ folder should be writeable
define("compressCache",true);  // Determines if HTML should have extra whitespace removed as well as cached. Results in a slightly smaller file
define("readmore", true);  //  Enable/Disable the Read More button on blog posts. This area needs refined still
define("home","home");  //  Defining the Home Page, can be set to a category

# Site Specific codes you need to fill out.
define("akismet","ff2ea92ed147");  //  Akismet key, used for filtering out spam comments, signup for a key on https://akismet.com/signup/
define("ga", "");  // Setting the Google Analytics Key for the site.

# Stuff past this point is still very much a work in progress. Dates are rough and ready in terms of implementation, needing lots of work to get them really ready for production. phpThumb stuff was implemented for one tested, to use it you'll need to add a phpThumb directory to the root of the site.

# Optional formatting for content
define('dateFormatBefore',"Posted on ");
define("dateFormat",'d/m/Y'); // Required if you want post dates to show up. Replaces <!--[TimeStamp]--> in posts.
define('dateFormatAfter',' by <!--[Author]-->');

# Optional extras, not currently required
//define("phpthumb",'/phpthumb/phpThumb.php?src=##FILE##&w=550&h=120&fltr[]=bord|2|0|0|333333'); // In place in case you want to use phpthumb for image cropping and resizing