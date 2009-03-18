<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Softbank/Function.php';


$lime = new lime_test();

$module = new Mobile_Profile_Softbank_Function();
$result = $module->collect();

// いくつか抜き出してチェック
$sample_data = array(
    '831T' => array(
        'model' => '831T',
        'lcd'   => array(
            'width'  => '240',
            'height' => '400',
        ),
        'memory'    => 'microSDHC',
        'bluetooth' => '×',
        'ir'        => array(
            'IrMC Ver.1.1',
        ),
        'qr'        => '○',
        'tv'        => array(
            '×',
        ),
        'highspeed' => '○',
        'camera'    => '○',
    ),
    '905SH' => array(
        'model' => '905SH',
        'lcd'   => array(
            'width'  => '240',
            'height' => '320',
        ),
        'memory'    => 'miniSD',
        'bluetooth' => '○',
        'ir'        => array(
            '○',
        ),
        'qr'        => '○',
        'tv'        => array(
            'ワンセグ',
            'アナログ',
        ),
        'highspeed' => '×',
        'camera'    => '○',
    ),
    '804NK' => array(
        'model' => '804NK',
        'lcd'   => array(
            'width'  => '240',
            'height' => '320',
        ),
        'memory'    => 'miniSD',
        'bluetooth' => '1.2',
        'ir'        => array(
            'IrLAP Ver.1.1',
            'IrLMP Ver.1.1',
            'IrTinyTP Ver.1.1',
            'IrComm Ver.1.0',
        ),
        'qr'        => '×',
        'tv'        => array(
            '×',
        ),
        'highspeed' => '×',
        'camera'    => '○',
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
