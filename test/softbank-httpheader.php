<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Softbank/Httpheader.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Softbank_Httpheader();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    '831T' => array(
        'model'   => '831T',
        'device'  => '831T',
        'display' => array(
            'width'  => '240',
            'height' => '400',
        ),
        'color' => array(
            'is_color' => true,
            'depth'    => '262144',
        ),
        'sound'        => '×',
        'smaf'         => '128/pcm/grf/rs',
        'display-info' => '×',
        'unique-id'    => null,
    ),
    'J-DN02' => array(
        'model'   => 'J-DN02',
        'device'  => 'J-DN02',
        'display' => array(
            'width'  => '116',
            'height' => '122',
        ),
        'color' => array(
            'is_color' => false,
            'depth'    => '4',
        ),
        'sound'        => '4.0',
        'smaf'         => '×',
        'display-info' => '×',
        'unique-id'    => null,
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
