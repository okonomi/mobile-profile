<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Softbank/Appli.php';


$lime = new lime_test();

$module = new Mobile_Profile_Softbank_Appli();
$result = $module->collect();

// いくつか抜き出してチェック
$sample_data = array(
    '831T' => array(
        'model'     => '831T',
        'heap'      => '5M',
        'cldc'      => '1.1',
        'midp'      => '2.0',
        'extension' => 'MEXA',
        'felica'    => '×',
        'location'  => '○',
        'size'      => array(
            'jad'         => '6K',
            'jar'         => '1024K',
            'recordstore' => '3M',
            'total'       => '4M',
        ),
    ),
    '930SC' => array(
        'model'     => '930SC',
        'heap'      => '×',
        'cldc'      => '×',
        'midp'      => '×',
        'extension' => '×',
        'felica'    => '×',
        'location'  => '×',
        'size'      => array(
            'jad'         => '×',
            'jar'         => '×',
            'recordstore' => '×',
            'total'       => '×',
        ),
    ),
    '702NKII(NOKIA 6680)' => array(
        'model'     => '702NKII(NOKIA 6680)',
        'heap'      => 'ｱﾌﾟﾘにより異なる',
        'cldc'      => '1.1',
        'midp'      => '2.0',
        'extension' => '×',
        'felica'    => '×',
        'location'  => '×',
        'size'      => array(
            'jad'         => '6K',
            'jar'         => '1024K',
            'recordstore' => '512K',
            'total'       => '1M',
        ),
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
