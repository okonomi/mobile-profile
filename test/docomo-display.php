<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Docomo/Display.php';


$lime = new lime_test();

$module = new Mobile_Profile_Docomo_Display();
$result = $module->collect();

// いくつか抜き出してチェック
$sample_data = array(
    'D501i' => array(
        'device'    => 'D501i',
        'model'     => 'D501i',
        'character' => array(
            array(
                'char' => '8',
                'line' => '6',
            ),
        ),
        'browser' => array(
            array(
                'width'  => '96',
                'height' => '72',
            ),
        ),
        'display' => array(),
        'color'   => array(
            'is_color' => false,
            'depth'    => '2',
        ),
    ),
    'D502i' => array(
        'device'    => 'D502i',
        'model'     => 'D502i',
        'character' => array(
            array(
                'char' => '8',
                'line' => '7',
            ),
        ),
        'browser' => array(
            array(
                'width'  => '96',
                'height' => '90',
            ),
        ),
        'display' => array(
            '待受アニメ' => array(
                'width'  => '96',
                'height' => '64',
            ),
        ),
        'color'   => array(
            'is_color' => true,
            'depth'    => '256',
        ),
    ),
    'N502i' => array(
        'device'    => 'N502i',
        'model'     => 'N502i',
        'character' => array(
            array(
                'char' => '10',
                'line' => '10',
            ),
        ),
        'browser' => array(
            array(
                'width'  => '118',
                'height' => '128',
            ),
        ),
        'display' => array(
            '時計表示OFF時' => array(
                'width'  => '118',
                'height' => '114',
            ),
            '時計表示ON時' => array(
                'width'  => '118',
                'height' => '70',
            ),
        ),
        'color'   => array(
            'is_color' => false,
            'depth'    => '4',
        ),
    ),
    'N02A' => array(
        'device'    => 'N02A',
        'model'     => 'N-02A',
        'character' => array(
            'デフォルト' => array(
                'char' => '12',
                'line' => '16',
            ),
            'ユーザ設定特大' => array(
                'char' => '8',
                'line' => '10',
            ),
            'ユーザ設定大' => array(
                'char' => '10',
                'line' => '13',
            ),
            'ユーザ設定小' => array(
                'char' => '15',
                'line' => '20',
            ),
        ),
        'browser' => array(
            array(
                'width'  => '240',
                'height' => '320',
            ),
        ),
        'display' => array(
            array(
                'width'  => '480',
                'height' => '854',
            ),

        ),
        'color' => array(
            'is_color' => true,
            'depth'    => '262144',
        ),
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
