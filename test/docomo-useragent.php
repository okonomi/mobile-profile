<?php
define('BASE', dirname(__FILE__));
set_include_path(BASE."/..".PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Docomo/Useragent.php';


$lime = new lime_test();

$lime->comment('useragent');

$module = new Mobile_Profile_Collector_Docomo_Useragent();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'D501i' => array(
        'device'     => 'D501i',
        'model'      => 'D501i',
        'series'     => '501i',
        'version'    => '1.0',
        'cache'      => '5',
        'generation' => 'mova',
    ),
    'D505i' => array(
        'device'     => 'D505i',
        'model'      => 'D505i',
        'series'     => '505i',
        'version'    => '5.0',
        'cache'      => '20',
        'generation' => 'mova',
    ),
    'P703imyu' => array(
        'device'     => 'P703imyu',
        'model'      => 'P703iμ',
        'series'     => '703i',
        'version'    => '6.0',
        'cache'      => '100',
        'generation' => 'FOMA',
    ),
    'F880iES' => array(
        'device'     => 'F880iES',
        'model'      => 'FOMAらくらくホン',
        'series'     => '880i',
        'version'    => '5.0',
        'cache'      => '100',
        'generation' => 'FOMA',
    ),
    'N02A' => array(
        'device'     => 'N02A',
        'model'      => 'N-02A',
        'series'     => null,
        'version'    => '7.2',
        'cache'      => '100',
        'generation' => 'FOMA',
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['device']])) {
        $lime->is_deeply($value, $sample_data[$value['device']]);
        unset($sample_data[$value['device']]);
    }
}

$lime->is(count($sample_data), 0);
