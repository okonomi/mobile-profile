<?php
error_reporting(E_ALL|E_STRICT);
set_include_path(dirname(__FILE__)."/..".PATH_SEPARATOR.get_include_path());


$class    = $argv[1];
$filename = str_replace('_', '/', $class).'.php';
require_once $filename;

$module = new $class();
$result = $module->collect();
print_r($result);
