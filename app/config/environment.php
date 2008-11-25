<?php

/* PHP SPECIFIC SETTINGS ------------------------------------------------- */

// PHP error reporting and display settings
error_reporting(E_STRICT | E_ALL);
ini_set('display_errors','1');

// Default timezone
date_default_timezone_set('UTC');



/* APPLICATION DEFAULTS -------------------------------------------------- */

// Default language
define('DEFAULT_LANG', 'en');

// Default controller called by the framework
define('DEFAULT_CONTROLLER', 'dashboard');

// Default controller action
define('DEFAULT_ACTION', 'index');

// Default layout called by the controller
define('DEFAULT_LAYOUT', 'master');

// Default view container called by a layout
define('DEFAULT_CONTAINER', 'main');

// The file extension of Views and Layouts
// You don't need to add the ".". Just the name of the file extension
define('VIEW_EXTENSION', 'tpl');

// Edit this to change the naming format of the log files
define('LOG_FORMAT', 'Y-m');

// Error namespace for $GLOBALS[ERRORS] array
define('ERRORS', 'errors');