<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Docomo.php';


$lime = new lime_test();

$collector = new Mobile_Profile_Collector_Docomo();
$result = $collector->collect();
