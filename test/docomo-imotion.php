<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Docomo/Imotion.php';


$lime = new lime_test();

$module = new Mobile_Profile_Docomo_Imotion();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'F2051' => array(
        'device'   => 'F2051',
        'model'    => 'F2051',
        'version'  => '1',
        'filesize' => '307200',
        'telop'    => true,
        '3d'       => false,
    ),
    'N03A' => array(
        'device'   => 'N03A',
        'model'    => 'N-03A',
        'version'  => '7',
        'filesize' => '10485760',
        'telop'    => false,
        '3d'       => false,
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['device']])) {
        $lime->is_deeply($value, $sample_data[$value['device']]);
        unset($sample_data[$value['device']]);
    }
    $lime->is(count($value), 6);
}

$lime->is(count($sample_data), 0);
