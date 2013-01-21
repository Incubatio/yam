<?php
/** CONSTANTS + AUTOLOADER **/
// Define environement
//$shortopts = "env::config_path::migration_path::";
//var_dump(getopt($shortopts));
//die;
define('ENV', 'development');
ini_set('display_errors', true);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

// Define includes paths
define('ROOT_PATH',  dirname(dirname(__FILE__)));
define('LIB_PATH',  ROOT_PATH . '/lib');
define('VENDOR_PATH',  ROOT_PATH . '/vendor');
set_include_path( PATH_SEPARATOR . LIB_PATH . PATH_SEPARATOR . VENDOR_PATH);

// set up autoloader
//spl_autoload_register(function ($class) { include str_replace('\\', '/', $class) . '.php'; });
include_once ROOT_PATH . '/app/autoload_register.php';

$resources = include_once(ROOT_PATH . '/app/bootstrap.php');
array_shift($argv);
$resources['argv'] = $argv;

$application = new \Incube\Application\EventApplication('shootmania_admin', $resources['event_manager']);
$application->set_resources($resources)->start();
