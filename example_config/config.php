<?php

/* $Id$ */

/* Set database values in config_local.php
$GLOBALS['DATABASE'] = array(
	'host'    => 'localhost',
	'user'    => 'username',
	'pass'    => 'password',
	'db'      => 'database',
	'charset' => 'utf8'
);
*/

// Set Site/Client name
setConfig("BLACKBIRD_CLIENT","Blackbird");
// Set users table
setConfig('BLACKBIRD_USERS_TABLE','cms_users');

// If you want to display an RSS feed in the homepage, set the RSS URL here
// setConfig("CMS_NEWS_FEED",'http://url.com/for/rss/feed');

// ini_set('display_errors',1);
// error_reporting(E_ALL);

// Get DB settings from local config_local.php file
(@include 'db.php') || die('<h1>Site not configured.</h1>');

