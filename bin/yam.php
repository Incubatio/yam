<?php
// Example: ./bin/yam -e=prod -m=/bin/test -c=/test/test --environment=prod --migration_folder=/bin/test2 --config_folder=/test/test2 migrate up

/** CONSTANTS + AUTOLOADER **/
// Define includes paths
define('ROOT_PATH',  dirname(dirname(__FILE__)));

// Define environement
$shortopts  = "e::" . "m::" . "c::";
$longopts  = array( "environment::", "migration_folder::",  "config_folder::");
$opts = getopt($shortopts, $longopts);

$options = array();
$options['environment'] = array_key_exists('environment', $opts) ? $opts['environment'] : (array_key_exists('e', $opts) ? $opts['e'] : 'development');
$options['config_folder'] = array_key_exists('config_folder', $opts) ? $opts['config_folder'] : (array_key_exists('c', $opts) ? $opts['m'] : ROOT_PATH . '/app/etc');
$options['migration_folder'] = array_key_exists('migration_folder', $opts) ? $opts['migration_folder'] : (array_key_exists('m', $opts) ? $opts['m'] : './migrations');

$environments = array('development', 'production', 'testing', 'staging');
foreach($environments as $e) {
    if(preg_match('/^' . $options['environment'] . '/', $e)) {
        $options['environment'] = $e;
        break;
    }
}



define('ENV', $options['environment']);
ini_set('display_errors', true);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Paris');

//define('LIB_PATH',  ROOT_PATH . '/lib');
define('VENDOR_PATH',  ROOT_PATH . '/vendor');
set_include_path( PATH_SEPARATOR . VENDOR_PATH);

// set up autoloader
//spl_autoload_register(function ($class) { include str_replace('\\', '/', $class) . '.php'; });
// include_once ROOT_PATH . '/app/autoload_register.php';
(@include_once __DIR__ . '/../vendor/autoload.php') || @include_once __DIR__ . '/../../../autoload.php';

include_once(ROOT_PATH . '/app/bootstrap.php');
$bootstrap = new Bootstrap(array('options' => $options, 'argv' => $argv));
$resources =  $bootstrap->load();


$application = new \Incube\Application\EventApplication('shootmania_admin', $resources['event_manager']);
$application->set_resources($resources)->start();
