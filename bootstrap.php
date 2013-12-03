<?php

require_once './code/Ff/Autoload.php';

spl_autoload_register('Ff\\Autoload::load');

$root = __DIR__;

define('DIR_ROOT', $root);
define('DIR_CODE', $root . '/code/');
define('DIR_RUNTIME', $root . '/runtime/');
define('DIR_INTERFACE', $root . '/interface/');

Ff\Autoload::addIncludePath(array(
    $root . '/code',
    $root . '/runtime',
    $root . '/interface',
    $root
));
