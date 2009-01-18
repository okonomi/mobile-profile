<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Docomo/Spec.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Docomo_Spec();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'L01A' => array(
        'device'      => 'L01A',
        'model'       => 'L-01A',
        'html'        => '6.0',
        'xhtml'       => '2.0',
        'iappli'      => 'DoJa-4.1LE',
        'felica'      => '－',
        'flash'       => '1.1',
        'imotion'     => 'MP4',
        'vlive'       => '○',
        'pdf'         => '－',
        'charaden'    => '－',
        'decomail'    => array(
            'version'        => '3.0',
            'send'           => true,
            'edit'           => true,
            'template'       => true,
            'template_dl'    => true,
            'template_title' => true,
        ),
        'decomeanime' => '－',
        'toruca'      => '－',
        'ichannel'    => 'b',
        'emoji'       => 'b',
        'gps'         => '－',
        'frame'       => '○',
        'menuicon'    => '○',
        'kisekae'     => '－',
        'machichara'  => '－',
        'iconcier'    => '－',
        'barcode'     => '3.0',
        'ssl'         => 'd',
        'drm'         => 'c',
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['device']])) {
        $lime->is_deeply($value, $sample_data[$value['device']]);
        unset($sample_data[$value['device']]);
    }
//    $lime->is(count($value), 26);
}

$lime->is(count($sample_data), 0);
