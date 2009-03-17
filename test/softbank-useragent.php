<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Softbank/Useragent.php';


$lime = new lime_test();

$module = new Mobile_Profile_Softbank_Useragent();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    '831T' => array(
        'model'     => '831T',
        'useragent' => array(
            'browser'     => 'SoftBank/1.0/831T/TJ001[/Serial] Browser/NetFront/3.3 Profile/MIDP-2.0 Configuration/CLDC-1.1',
            'appli'       => 'SoftBank/1.0/831T/TJ001[/Serial] Java/Java/1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1',
            'widget'      => null,
            'flash'       => 'SoftBank/1.0/831T/TJ001[/Serial] Flash/Flash-Lite/2.0',
            'fullbrowser' => null,
        ),
    ),
    '304T' => array(
        'model'     => '304T',
        'useragent' => array(
            'browser'     => 'J-PHONE/3.0/V304T',
            'appli'       => null,
            'widget'      => null,
            'flash'       => null,
            'fullbrowser' => null,
        ),
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
