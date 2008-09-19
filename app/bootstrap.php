<?php

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
define('LIB','lib' . DS);

// Web root ??? what is this for ???
define('WEB_ROOT','');

// Server/domain name with http(s)://
define('WWW','http' . (@$_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . '/');



define('BLACKBIRD_TABLE_PREFIX','cms_');

/* LOAD REQUIRED FILES --------------------------------------------------- */

// Environment config
require_once CONFIG . 'environment.php';

// Core MVC framework classes
require_once LIB . 'BrickHouse' . DS . 'ErrorHandler.php';
require_once LIB . 'BrickHouse' . DS . 'Model.php';
require_once LIB . 'BrickHouse' . DS . 'ControllerFront.php';
require_once LIB . 'BrickHouse' . DS . 'Controller.php';

// Bobolink classes
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'Db.interface.php';
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'AdaptorMysql.class.php';

require_once LIB . 'Bobolink' . DS . 'utils' . DS . 'Utils.class.php';


/* LOAD PLUGINS ---------------------------------------------------------- */
require_once LIB . 'BrickHouse' . DS . 'plugins' . DS . 'sitemap.php';
//require_once ROOT . APP . 'plugins' . DS . 'pre_render.php';

//local extended classes
require_once APP . '_ControllerFront.php';
require_once APP . '_Controller.php';


/* CONTROLLER/ROUTER ----------------------------------------------------- */
//error handling
//$error = ErrorHandler::getInstance();
set_error_handler(array("ErrorHandler","capture"));


// Only connect to the database if it is configured
if (isset($GLOBALS['DATABASE'])) {
	AdaptorMysql::getInstance();
	AdaptorMysql::sql('SET NAMES utf8');
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

$tA = explode("/",substr($_SERVER['PHP_SELF'],1,-(strlen('index.php') + 1)));


/* DISPATCH -------------------------------------------------------------- */

$controller->dispatch($routes);
