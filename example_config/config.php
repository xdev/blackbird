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
// setConfig('BLACKBIRD_CLIENT','Client Name Database');

// Set alternate Blackbird table prefix (for Blackbird tables only)
// setConfig('BLACKBIRD_TABLE_PREFIX','_blackbird_');

// Set users table
// setConfig('BLACKBIRD_USERS_TABLE','_blackbird_users');

// Set timezone
// date_default_timezone_set('Europe/London');

// PHP error reporting
// ini_set('display_errors',1);
// error_reporting(E_ALL);

// Include db.php file for $GLOBALS['DATABASE'] settings
(@include 'db.php') || die('<h1>Database not configured.</h1>');
