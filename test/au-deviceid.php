<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Au/Deviceid.php';


$lime = new lime_test();

$module = new Mobile_Profile_Au_Deviceid();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'Xmini' => array(
        'model'    => 'Xmini',
        'deviceid' => 'SN3H',
    ),
    'W32S' => array(
        'model'    => 'W32S',
        'deviceid' => 'SN33/SN35',
    ),
    'W31SA/SA II' => array(
        'model'    => 'W31SA/SA II',
        'deviceid' => 'SA33',
    ),
    'A5401CA/CA II' => array(
        'model'    => 'A5401CA/CA II',
        'deviceid' => 'CA23',
    ),
    'W44K IIカメラなしモデル' => array(
        'model'    => 'W44K IIカメラなしモデル',
        'deviceid' => 'KC3E',
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
