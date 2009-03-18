<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Au/Java.php';


$lime = new lime_test();

$module = new Mobile_Profile_Au_Java();
$result = $module->collect();

// いくつか抜き出してチェック
$sample_data = array(
    'C452CA' => array(
        'model'   => 'C452CA',
        'version' => 'Phase1',
    ),
    'C451H' => array(
        'model'   => 'C451H',
        'version' => 'Phase1',
    ),
    'A3015SA' => array(
        'model'   => 'A3015SA',
        'version' => 'Phase2.5',
    ),
    'A5401CA II' => array(
        'model'   => 'A5401CA II',
        'version' => 'Phase2.5',
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['model']])) {
        $lime->is_deeply($value, $sample_data[$value['model']]);
        unset($sample_data[$value['model']]);
    }
    $lime->is(count($value), 2);
}

$lime->is(count($sample_data), 0);
