<?php

// Used to manage user-overrides.
// This would be easier if we simply put it all into a GLOBAL variable.. mmkay.

$tempObj = Array();

function setConfig($name,$value)
{
	global $tempObj;
	$tempObj[$name] = $value;
}

function createConstants()
{
	global $tempObj;
	foreach($tempObj as $key=>$value){
		if(!is_array($value) && !defined($key)){
			define($key,$value);
		}
	}
	unset($tempObj);
}

/* DEFINE PATHS ---------------------------------------------------------- */
// Directory splitter
define('DS', DIRECTORY_SEPARATOR);

// Site/app root (full path)
define('ROOT',$_SERVER['DOCUMENT_ROOT'] . DS);

// Path to application (relative to index.php)
define('APP',(dirname(__FILE__)) . DS);

// Path to application config (full path)
define('CONFIG',APP . 'config' . DS);

// Path to application controllers (full path)
define('MODELS',APP . 'models' . DS);

// Path to application views (full path)
define('VIEWS',APP . 'views' . DS);

// Path to application controllers (full path)
define('CONTROLLERS',APP . 'controllers' . DS);

// Path to libraries - relative to index.php
setConfig('LIB','lib' . DS);

// Web root ??? what is this for ???
setConfig('WEB_ROOT',ROOT);

// Server/domain name with http(s)://
setConfig('WWW','http' . (@$_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . '/');

// Database table prefix
setConfig('BLACKBIRD_TABLE_PREFIX','_blackbird_');

// Database users table, typically changed with a Session override
setConfig('BLACKBIRD_USERS_TABLE','_blackbird_users');

// Default client name
setConfig("BLACKBIRD_CLIENT","Blackbird");

// Version number of this software
define("BLACKBIRD_VERSION","2.0.0");

// Required database schema
define("REQUIRED_SCHEMA_VERSION","2.0.0");

// Environment config
require_once CONFIG . 'environment.php';


//LOAD UP THE CUSTOM CONFIG
//convert to array.. take next to last segment ... append _config
$tA = explode(DS,APP);
$base = $tA[count($tA)-3];
$file = '..' . DS . $base . '_config' . DS . 'config.php';

define('INSTALL_FOLDER',$base);

// Set location of custom project-based config info
setConfig('CUSTOM','..' . DS . INSTALL_FOLDER . '_config' . DS);

// Bring it in or abort
(!require($file)) ? die('<h1>No custom config found = Fail!</h1>') : '';	

// Define all variables created with setConfig
createConstants();

/* LOAD REQUIRED FILES --------------------------------------------------- */
// Core MVC framework classes
require_once LIB . 'Brickhouse' . DS . 'ErrorHandler.php';
require_once LIB . 'Brickhouse' . DS . 'Model.php';
require_once LIB . 'Brickhouse' . DS . 'ControllerFront.php';
require_once LIB . 'Brickhouse' . DS . 'Controller.php';

// Bobolink classes
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'Db.interface.php';
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'AdaptorMysql.class.php';
require_once LIB . 'Bobolink' . DS . 'utils' . DS . 'Utils.class.php';
require_once LIB . 'Bobolink' . DS . 'forms' . DS . 'Forms.class.php';
require_once LIB . 'Bobolink' . DS . 'xml' . DS . 'XmlToArray.class.php';


/* LOAD PLUGINS ---------------------------------------------------------- */
require_once LIB . 'Brickhouse' . DS . 'plugins' . DS . 'sitemap.php';
//require_once ROOT . APP . 'plugins' . DS . 'pre_render.php';

//local extended classes
require_once CONTROLLERS . '_ControllerFront.php';
require_once CONTROLLERS . '_Controller.php';


/* CONTROLLER/ROUTER ----------------------------------------------------- */
//error handling
//$error = ErrorHandler::getInstance();
set_error_handler(array("ErrorHandler","capture"));


// Only connect to the database if it is configured
if (isset($GLOBALS['DATABASE'])) {
	AdaptorMysql::getInstance();
}

// Controller
$controller = _ControllerFront::getInstance();
// Router
//needs to follow the Front Controller so we can utilize the pre-parsed URI
$uA = $controller->getUri();
$uri = $uA['array'];

$routes = array();

//pull in predefined routes
require_once CONFIG . 'routes.php';

$tA = explode(DS,substr($_SERVER['PHP_SELF'],1,-(strlen('index.php') + 1)));


/* DISPATCH -------------------------------------------------------------- */

$controller->dispatch($routes);
