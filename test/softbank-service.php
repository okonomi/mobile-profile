<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Softbank/Service.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Softbank_Service();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    '831T' => array(
        'model'       => '831T',
        'appli'       => '○',
        'widget'      => '×',
        'flash'       => 'Flash Lite 2.0',
        'gps'         => '○',
        'agps'        => '○',
        'felica'      => '×',
        'fullbrowser' => '×',
    ),
    '304T' => array(
        'model'       => '304T',
        'appli'       => '×',
        'widget'      => '×',
        'flash'       => '×',
        'gps'         => '○',
        'agps'        => '×',
        'felica'      => '×',
        'fullbrowser' => '×',
    ),
    'J-D05' => array(
        'model'       => 'J-D05',
        'appli'       => '○',
        'widget'      => '×',
        'flash'       => '×',
        'gps'         => '○',
        'agps'        => '×',
        'felica'      => '×',
        'fullbrowser' => '×',
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['model']])) {
        $lime->is_deeply($value, $sample_data[$value['model']]);
        unset($sample_data[$value['model']]);
    }
    $lime->is(count($value), 8);
}

$lime->is(count($sample_data), 0);
