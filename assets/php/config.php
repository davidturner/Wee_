<?php

date_default_timezone_set('Europe/Belfast');

$site->seperator = ' | '; # Used for page title
$site->title = 'Portfolio of David Turner'; # Used for page title

$site->author->name = 'David Turner';
$site->author->email = 'hi@davidturner.name';
$site->author->twitter = 'HerrWulf';

$site->analytics = ''; # Google Analytics Code
$site->theme = 'basic'; # Site Theme. Called throughout site for css/js stuff so I can switch themes with ease

$site->desc = ''; # Site Description
$site->tags = ''; # Site Tags
$site->ext = 'md'; # Post File Extension. Common extensions: md, mdown, text, markdown

$site->posts->page = 5; # Posts Per Page
$site->posts->feed = 20; #Posts in RSS Feed

$site->nofeed = array('.','..','.DS_Store','pages','errors','drafts'); # Ignore these for RSS feeds
$site->nocomments = array('pages'); # Don't allow comments on

$site->cache->active = 1; # [1|0] 1 = cache site content
$site->cache->compress = 1; # [1|0] 1 = compress cache
$site->more = 1; # [1|0] 1 = Shows link with text defined below
$site->readmore = 'Read More'; # Text for read more links
$site->home = 'home'; # default page
$site->redir = 0; # [1|0] 1 = Redirect if directly on domain?

$site->date->format->before = 'Posted on ';
$site->date->format->structure = 'd/m/Y';
$site->date->format->after = ' by <span class="author"><!--[Author]--></span>';

$site->closecomments = 0; # [1|0] 1 closes comments across the site
$site->comments = 'filtered'; # [filtered|all|none] all will save spam comments in as spam-file.md so they need to be approved

$site->url = 'http://'.str_replace('www.', '', $_SERVER['SERVER_NAME']); # can be used to ensure site is on the correct protocol
$site->process = 1; # Used for form validation in comments.
$site->nofeed = array('.','..','.DS_Store','pages','errors','drafts');
$site->nocomments = array('pages');

$site->copyright = '&copy; '.@date('Y').' '.$site->author->name; # meta copyright information
$site->akismet = 'ff2ea92ed147';
$site->query = $_SERVER['REQUEST_URI']; # Gets the extra bits of the URL past the site's domain
$site->slug = explode('/',substr($site->query,1,-1)); # Individual URL components, used for site logic