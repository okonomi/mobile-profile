<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Docomo/Appli.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Docomo_Appli();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'F503i' => array(
        'device'    => 'F503i',
        'model'     => 'F503i',
        'profile'   => 'DoJa-1.0',
        'applisize' => array(
            'jar'        => '10',
            'scratchpad' => '10',
        ),
        'drawarea' => array(
            'panel' => array(
                'width'  => '120',
                'height' => '130',
            ),
            'canvas' => array(
                'width'  => '120',
                'height' => '130',
            ),
        ),
        'heap' => array(
            'java'   => '600',
            'native' => null,
            'widget' => null,
        ),
        'font' => array(
            'width'  => '12',
            'height' => '12',
        ),
    ),
    'N02A' => array(
        'device'    => 'N02A',
        'model'     => 'N-02A',
        'profile'   => 'Star-1.0',
        'applisize' => array(
            'jar'        => '2048',
            'scratchpad' => null,
        ),
        'drawarea' => array(
            'panel' => array(
                'width'  => '480',
                'height' => '854',
            ),
            'canvas' => array(
                'width'  => '480',
                'height' => '854',
            ),
        ),
        'heap' => array(
            'java'   => '32768',
            'native' => null,
            'widget' => '3527',
        ),
        'font' => array(
            'width'  => '24',
            'height' => '24',
        ),
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['device']])) {
        $lime->is_deeply($value, $sample_data[$value['device']]);
        unset($sample_data[$value['device']]);
    }
    $lime->is(count($value), 7);
}

$lime->is(count($sample_data), 0);
