<?php
define('BASE', dirname(__FILE__));
ini_set("include_path", BASE."/..".PATH_SEPARATOR.ini_get("include_path"));

require_once 'Mobile/Profile.php';


$carrier = $argv[1];


$profile = & new Mobile_Profile();
$result = $profile->get($carrier);
print_r($result);
