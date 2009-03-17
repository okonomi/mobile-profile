<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Docomo/Menuicon.php';


$lime = new lime_test();

$module = new Mobile_Profile_Docomo_Menuicon();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    'N505iS' => array(
        'device' => 'N505iS',
        'model'  => 'N505iS',
        'item'   => array(
            'iアプリ',
            'カメラ',
            'マイサウンド',
            'ユーザデータ',
            'マイピクチャ',
            'ツールBOX',
            'miniSD',
            'カスタムメニュー',
            '各種設定',
        ),
        'icon_size' => array(
            'width'  => '78',
            'height' => '76',
        ),
        'need_embed' => true,
        'background_size' => array(
            'width'  => '240',
            'height' => '320',
        ),
    ),
    'L01A' => array(
        'device' => 'L01A',
        'model'  => 'L-01A',
        'item'   => array(
            'メール',
            'iモード',
            'iアプリ',
            '電話帳',
            'データBOX',
            'MUSIC',
            'ワンセグ',
            'カメラ',
            'ステーショナリー',
            '自局番号',
            '設定',
            'LifeKit',
        ),
        'icon_size' => array(
            'width'  => '48',
            'height' => '48',
        ),
        'need_embed' => false,
        'background_size' => null,
    ),
);

foreach ($result as $value) {
    if (isset($sample_data[$value['device']])) {
        $lime->is_deeply($value, $sample_data[$value['device']]);
        unset($sample_data[$value['device']]);
    }
    $lime->is(count($value), 6);
}

$lime->is(count($sample_data), 0);
