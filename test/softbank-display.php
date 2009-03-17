<?php
error_reporting(E_ALL|E_STRICT);
define('BASE', dirname(__FILE__));
set_include_path(dirname(BASE).PATH_SEPARATOR.get_include_path());

require_once BASE.'/t/lime.php';
require_once 'Mobile/Profile/Softbank/Display.php';


$lime = new lime_test();

$module = new Mobile_Profile_Softbank_Display();
$result = $module->scrape();

// いくつか抜き出してチェック
$sample_data = array(
    '831T' => array(
        'model'          => '831T',
        'browser_screen' => array(
            array(
                'width'  => '234',
                'height' => '339',
            ),
        ),
        'widget_solo'   => array(
            array(),
        ),
        'widget_wallp'  => array(
            array(),
        ),
        'browser_chars' => array(
            array(
                '大' => array(
                    'char' => '18',
                    'line' =>  '9',
                ),
                '中' => array(
                    'char' => '18',
                    'line' => '11',
                ),
                '小さめ' => array(
                    'char' => '23',
                    'line' => '13',
                ),
                '小' => array(
                    'char' => '26',
                    'line' => '18',
                ),
                '極小' => array(
                    'char' => '39',
                    'line' => '28',
                ),
            ),
        ),

        'appli_screen' => array(
            'QQVGA' => array(
                'width'  => '120',
                'height' => '130',
            ),
            'QVGA' => array(
                'width'  => '240',
                'height' => '260',
            ),
            'WQVGA' => array(
                'width'  => '240',
                'height' => '320',
            ),
            'WQVGA(独自ｻｲｽﾞ)' => array(
                'width'  => '240',
                'height' => '340',
            ),
        ),
        'appli_font' => array(
            'QQVGA' => array(
                'width'  => '12',
                'height' => '12',
            ),
            'QVGA' => array(
                'width'  => '18',
                'height' => '18',
            ),
            'WQVGA' => array(
                'width'  => '18',
                'height' => '18',
            ),
            'WQVGA(独自ｻｲｽﾞ)' => array(
                'width'  => '18',
                'height' => '18',
            ),
        ),
        'flash' => array(
            'width'  => '236',
            'height' => '341',
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
