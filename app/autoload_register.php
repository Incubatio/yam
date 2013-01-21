<?php
spl_autoload_register(
    function ($class) {
        static $map;
        if (!$map) {
            $map = include ROOT_PATH . '/vendor/composer/autoload_classmap.php';
        }

        if (!isset($map[$class])) {
            return false;
        }
        return include $map[$class];
    }
);
