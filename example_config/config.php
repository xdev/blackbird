<?php

/* Set database values in db.php:

<<<

$GLOBALS['DATABASE'] = array(
	'host'    => 'localhost',
	'user'    => 'username',
	'pass'    => 'password',
	'db'      => 'database',
	'charset' => 'utf8'
);

>>>

*/

// Set Site/Client name
setConfig("BLACKBIRD_CLIENT","Blackbird");
// Set users table
#setConfig('BLACKBIRD_USERS_TABLE','_blackbird_users');
#setConfig('BLACKBIRD_TABLE_PREFIX','_blackbird_');
// ini_set('display_errors',1);
// error_reporting(E_ALL);

// Get DB settings from local config_local.php file
(@include 'db.php') || die('<h1>Site not configured.</h1>');