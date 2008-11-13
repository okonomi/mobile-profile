<?php

define('BASE', dirname(__FILE__));

ini_set("include_path", BASE."/..".PATH_SEPARATOR.ini_get("include_path"));
require_once "Mobile/Profile.php";


$profile = & new Mobile_Profile();
$result = $profile->get('au')->output('array');
print_r($result);
