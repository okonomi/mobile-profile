<?php
define('BASE', dirname(__FILE__));
ini_set("include_path", BASE."/..".PATH_SEPARATOR.ini_get("include_path"));


$class    = $argv[1];
$filename = str_replace('_', '/', $class).'.php';
require_once $filename;

$module = new $class();
$result = $module->scrape();
print_r($result);
