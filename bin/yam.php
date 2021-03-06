<?php

// Example: ./bin/yam -e=prod -m=/bin/test -c=/test/test --environment=prod --migration=/bin/test2 --config=/test/test2 migrate up

/** CONSTANTS + AUTOLOADER **/
// Define includes paths
define('ROOT_PATH',  dirname(__DIR__));

// Define environement
$shortopts  = "e::" . "m::" . "c::" . "b::";
$longopts  = array( "environment::", "migration::",  "config::", "bootstrap::");
$opts = getopt($shortopts, $longopts);

$options = array();
$options['environment'] = array_key_exists('environment', $opts) ? $opts['environment'] : (array_key_exists('e', $opts) ? $opts['e'] : 'development');
$options['config'] = array_key_exists('config', $opts) ? $opts['config'] : (array_key_exists('c', $opts) ? $opts['c'] : ROOT_PATH . '/app/etc/db.php');
$options['migration'] = array_key_exists('migration', $opts) ? $opts['migration'] : (array_key_exists('m', $opts) ? $opts['m'] : './migrations');
$options['bootstrap'] = array_key_exists('bootstrap', $opts) ? $opts['bootstrap'] : (array_key_exists('b', $opts) ? $opts['b'] : ROOT_PATH . '/app/bootstrap.php');
foreach($options as $k => $v) if(empty($v)) die('fatal error: "' . $k . '" option must not be empty, please provide a valid value or let the default one');

$environments = array('development', 'production', 'testing', 'staging');
foreach($environments as $e) {
    if(preg_match('/^' . $options['environment'] . '/', $e)) {
        $options['environment'] = $e;
        break;
    }
}



define('ENV', $options['environment']);
ini_set('display_errors', true);
error_reporting(-1);

date_default_timezone_set('Europe/Paris');

//define('LIB_PATH',  ROOT_PATH . '/lib');
define('VENDOR_PATH',  ROOT_PATH . '/vendor');
set_include_path( PATH_SEPARATOR . VENDOR_PATH);

// set up autoloader
//spl_autoload_register(function ($class) { include str_replace('\\', '/', $class) . '.php'; });
// include_once ROOT_PATH . '/app/autoload_register.php';
(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

include_once($options['bootstrap']);
$bootstrap = new Bootstrap(array('options' => $options, 'argv' => $argv));
$resources =  $bootstrap->load();


$application = new \Incube\Event\Application('shootmania_admin', $resources['event_manager']);
$application->set_resources($resources)->start();
