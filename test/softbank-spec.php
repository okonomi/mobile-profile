<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Softbank/Spec.php';


$lime = new lime_test();

$module = new Mobile_Profile_Softbank_Spec();

try {
    $module->collect();

    $lime->fail();
} catch (Exception $e) {
    $lime->pass();
}
