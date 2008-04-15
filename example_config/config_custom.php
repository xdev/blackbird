<?php

/* $Id$ */

/* Set database values in config_local.php
$GLOBALS['DATABASE'] = array(
	'host' => '',
	'user' => '',
	'pass' => '',
	'db'   => ''
);
*/

// Set Site/Client name
setConfig("CMS_CLIENT","Blackbird");
// Set users table
setConfig('CMS_USERS_TABLE','cms_users');

// If you want to display an RSS feed in the homepage, set the RSS URL here
// setConfig("CMS_NEWS_FEED",'http://url.com/for/rss/feed');

// ini_set('display_errors',1);
// error_reporting(E_ALL);

// Get DB settings from local config_local.php file
if (file_exists($_SERVER['DOCUMENT_ROOT'].'/cms_config/config_local.php')) {
	include 'config_local.php';
} else {
	die('<h1>Site not configured.</h1>');
}

