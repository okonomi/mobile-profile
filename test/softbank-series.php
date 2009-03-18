<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Softbank/Series.php';


$lime = new lime_test();

$module = new Mobile_Profile_Softbank_Series();
$result = $module->collect();

// いくつか抜き出してチェック
$sample_data = array(
    '831T' => array(
        'model'      => '831T',
        'series'     => 'SoftBank 3G series',
        'generation' => '3GC',
    ),
    '304T' => array(
        'model'      => '304T',
        'series'     => 'SoftBank 4-1 series(S!アプリ非対応)',
        'generation' => '2G',
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['model']])) {
        $lime->is_deeply($value, $sample_data[$value['model']]);
        unset($sample_data[$value['model']]);
    }
    $lime->is(count($value), 3);
}

$lime->is(count($sample_data), 0);
