<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Softbank/Format.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Softbank_Format();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    '831T' => array(
        'model' => '831T',
        'jpeg'  => '○',
        'png'   => '○',
        'gif'   => '○',
        'smaf'  => 'MA-7',
        'midi'  => '×',
        'mp4'   => '○',
    ),
    '304T' => array(
        'model' => '304T',
        'jpeg'  => '○',
        'png'   => '○',
        'gif'   => '×',
        'smaf'  => 'MA-3',
        'midi'  => '×',
        'mp4'   => '×',
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['model']])) {
        $lime->is_deeply($value, $sample_data[$value['model']]);
        unset($sample_data[$value['model']]);
    }
    $lime->is(count($value), 7);
}

$lime->is(count($sample_data), 0);
