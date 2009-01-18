<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Docomo/Flash.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Docomo_Flash();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'D505i' => array(
        'device'   => 'D505i',
        'model'    => 'D505i',
        'version'  => 'Flash Lite 1.0',
        'drawarea' => array(
            'browser' => array(
                'width'  => '240',
                'height' => '270',
            ),
            'display' => array(
                'width'  => '240',
                'height' => '320',
            ),
        ),
        'memory' => '200',
        'font'   => array(
            array(
                'width'  => '24',
                'height' => '24',
            ),
        ),
        'scalable_font' => false,
        'pointing'      => false,
        'inline'        => '1',
    ),
    'M702iG' => array(
        'device'   => 'M702iG',
        'model'    => 'M702iG',
        'version'  => 'Flash Lite 1.1',
        'drawarea' => array(
            'browser' => array(
                'width'  => '240',
                'height' => '267',
            ),
            'display' => null,
        ),
        'memory' => '600',
        'font'   => array(
            array(
                'width'  => '12',
                'height' => '12',
            ),
            array(
                'width'  => '16',
                'height' => '16',
            ),
            array(
                'width'  => '24',
                'height' => '24',
            ),
        ),
        'scalable_font' => false,
        'pointing'      => false,
        'inline'        => '1',
    ),
    'N01A' => array(
        'device'   => 'N01A',
        'model'    => 'N-01A',
        'version'  => 'Flash Lite 3.1',
        'drawarea' => array(
            'browser' => array(
                'width'  => '480',
                'height' => '640',
            ),
            'display' => array(
                'width'  => '480',
                'height' => '854',
            ),
        ),
        'memory' => '3072',
        'font'   => array(
            array(
                'width'  => '4',
                'height' => '4',
            ),
            array(
                'width'  => '96',
                'height' => '96',
            ),
        ),
        'scalable_font' => true,
        'pointing'      => true,
        'inline'        => '2',
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['device']])) {
        $lime->is_deeply($value, $sample_data[$value['device']]);
        unset($sample_data[$value['device']]);
    }
    $lime->is(count($value), 9);
}

$lime->is(count($sample_data), 0);
