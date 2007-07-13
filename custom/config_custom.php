<?php

// Set database values
$GLOBALS['DATABASE'] = array(
	'host' => 'localhost',
	'user' => 'root',
	'pass' => 'root',
	'db'   => 'blackbird'
);

// Set Site/Client name
setConfig("CMS_CLIENT","Blackbird");

// If you want to display an RSS feed in the homepage, set the RSS URL here
// setConfig("CMS_NEWS_FEED",'http://url.com/for/rss/feed');