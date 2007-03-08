<?php

if(file_exists('config_local.php')) require_once('config_local.php');
else{

	
	$GLOBALS['DATABASE'] = array(
		'host' => 'localhost',
		'user' => 'webdesigner',
		'pass' => 'rgbf00',
		'db'   => 'cms_dev'
		);

	
	define("LIB","/Volumes/xdev/WebServer/Documents/cms_dev/cms/_lib/");
	define("WEB_ROOT","");
	define("PLUGINS","plugins/");
	
}


define("XML_HEADER",'<?xml version="1.0" encoding="UTF-8"?>');
define("SERVER","");


define("CMS_CLIENT","Development");
define("CMS_TRIM","right");
define("CMS_DEFAULT_HOUR",19);
define("CMS_DEFAULT_MIN",30);
define("CMS_MAX_YEAR",2011);
define("CMS_MIN_YEAR",1990);
define("CMS_DATA_GRID_SORT_MAX",20);

define("CMS_NEWS_FEED",'');

define("CMS_ROOT",substr($_SERVER['PHP_SELF'],0,-strlen('index.php')));
define("CMS_VERSION","1.0");



?>