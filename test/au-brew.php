<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Au/Brew.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Au_Brew();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'Xmini' => array(
        'model'   => 'Xmini',
        'version' => '4.0',
    ),
    'B01K' => array(
        'model'   => 'B01K',
        'version' => '2.1',
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
