<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Au/Basic.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Au_Basic();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'Xmini' => array(
        'model'         => 'Xmini',
        'browser_type'  => 'WAP2.0',
        'display_color' => array(
            'is_color' => true,
            'depth'    => '65536',
        ),
        'display_chars' => array(
            'char' => '23',
            'line' => '11',
        ),
        'browser_screen' => array(
            'width'  => '233',
            'height' => '251',
        ),
        'display_screen' => array(
            array(
                'width'  => '240',
                'height' => '320',
            ),
        ),
        'format' => array(
            array(
                'gif'  => '○',
                'jpeg' => '○',
                'png'  => '○',
                'bmp4' => '−',
                'bmp2' => '−',
            ),
        ),
        'flash_version' => '2.0',
        'attach'        => '◎',
    ),
    'A5402S' => array(
        'model'         => 'A5402S',
        'browser_type'  => 'WAP2.0',
        'display_color' => array(
            'is_color' => true,
            'depth'    => '260000',
        ),
        'display_chars' => array(
            'char' => '20',
            'line' => '8',
        ),
        'browser_screen' => array(
            'width'  => '120',
            'height' => '160',
        ),
        'display_screen' => array(
            '高精細' => array(
                'width'  => '240',
                'height' => '320',
            ),
            '標準' => array(
                'width'  => '120',
                'height' => '160',
            ),
        ),
        'format' => array(
            array(
                'gif'  => '○',
                'jpeg' => '○',
                'png'  => '○',
                'bmp4' => '−',
                'bmp2' => '−',
            ),
        ),
        'flash_version' => null,
        'attach'        => '◎',
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['model']])) {
        $lime->is_deeply($value, $sample_data[$value['model']]);
        unset($sample_data[$value['model']]);
    }
    $lime->is(count($value), 9);
}

$lime->is(count($sample_data), 0);
