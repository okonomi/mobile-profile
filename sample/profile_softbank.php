<?php

define('BASE', dirname(__FILE__));

ini_set("include_path", BASE."/..".PATH_SEPARATOR.ini_get("include_path"));
require_once "Mobile/Profile.php";

if ($argc < 3) {
    exit;
}

// MOBILE CREATION(http://creation.mb.softbank.jp/)のログイン情報
$options = array(
    'username' => $argv[1],
    'password' => $argv[2],
);

$profile = & new Mobile_Profile();
$result = $profile->get('softbank', $options)->output('array');
print_r($result);
