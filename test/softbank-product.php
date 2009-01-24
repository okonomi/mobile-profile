<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Collector/Softbank/Product.php';


$lime = new lime_test();

$module = new Mobile_Profile_Collector_Softbank_Product();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    '着信メロディ' => array(
        'name'  => '着信メロディ',
        'model' => array(
            '502T',
            '403SH',
            '304T',
            '201SH',
        ),
    ),
    '着うたフル{r}' => array(
        'name'  => '着うたフル{r}',
        'model' => array(
            '931SH', '930SC', '930SH', '923SH', '922SH', '921P', '921SH',
            '920P', '920SC', '920SH', '920SH YK', '921T', '920T', '913SH',
            '913SH G', '912SH', '912T', '911SH', '911T', '910SH', '910T',
            '905SH', '904T', '904SH', '831T', '830CA', '830P', '830SH s',
            '830SH', '830T', '825SH', '824P', '824SH Active Line',
            '824SH Elegant Line', '824T', '823P', '823SH', '823T',
            '822P', '822SH', '822T', '821SC', '821SH', '821P',
            '821T', '820P', '820SC', '820SH', '816SH', '815SH',
            '815T PB', '815T', '814SH', '814T', '813SH', '813SH for Biz',
            '813T', '812SH', '812SH s', '812SH sII', '812T', '811SH',
            '811T', '810P', '810SH', '810T', '805SC', '804N', '803T', '709SC',
            '708SC', '707SC', '707SCII', '706N', '706P', '706SC', '705N', '705NK',
            '705P', '705Px', '705SC', '705SH', '705T',
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
