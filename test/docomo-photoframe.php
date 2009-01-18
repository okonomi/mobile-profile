<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Docomo/Photoframe.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Docomo_Photoframe();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'N251i' => array(
        'device' => 'N251i',
        'model'  => 'N251i',
        'size'   => array(
            array(
                'width'  => '120',
                'height' => '120',
            ),
            array(
                'width'  => '132',
                'height' => '158',
            ),
        ),
    ),
    'P504iS' => array(
        'device' => 'P504iS',
        'model'  => 'P504iS',
        'size'   => array(
            array(
                'width'  => '120',
                'height' => '120',
            ),
            array(
                'width'  => '132',
                'height' => '176',
            ),
            array(
                'width'  => '96',
                'height' => '64',
            ),
        ),
    ),
    'L01A' => array(
        'device' => 'L01A',
        'model'  => 'L-01A',
        'size'   => array(
            array(
                'width'  => '128',
                'height' => '96',
            ),
            array(
                'width'  => '176',
                'height' => '144',
            ),
            array(
                'width'  => '240',
                'height' => '400',
            ),
            array(
                'width'  => '320',
                'height' => '240',
            ),
            array(
                'width'  => '352',
                'height' => '288',
            ),
        ),
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['device']])) {
        $lime->is_deeply($value, $sample_data[$value['device']]);
        unset($sample_data[$value['device']]);
    }
    $lime->is(count($value), 3);
}

$lime->is(count($sample_data), 0);
