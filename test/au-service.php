<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Au/Service.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Au_Service();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'LISMO Player &LISMO Port' => array(
        'name'  => 'LISMO Player &LISMO Port',
        'model' => array(
            'W65T',
            'Xmini',
            'W64T',
            'AQUOSケータイW64SH',
            'W64SA',
            'Sportio',
            'W63SA',
            'フルチェンケータイ re',
            'Woooケータイ W63H',
            'EXILIMケータイ W63CA',
            'W62T',
            'W62SH',
            'Woooケータイ W62H',
            'G\'zOne W62CA',
            'W61T',
            'W61SA',
            'Cyber-shot (TM) ケータイ W61S',
            'W56T',
            'W54SA',
            'W54S',
        ),
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['name']])) {
        $lime->is_deeply($value, $sample_data[$value['name']]);
        unset($sample_data[$value['name']]);
    }
    $lime->is(count($value), 2);
}

$lime->is(count($sample_data), 0);
