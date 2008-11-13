<?php
require_once dirname(__FILE__) . '.php';


class Mobile_Profile_Collector_Docomo extends Mobile_Profile_Collector
{
    var $base_url = 'http://www.nttdocomo.co.jp/';
    //var $base_url = 'http://www.colinux/~kawakami/docomo/';

    var $spec_url = 'binary/pdf/service/imode/make/content/spec/imode_spec.pdf';

    var $url_list = array (
        'useragent'   => 'service/imode/make/content/spec/useragent/index.html',
        'display'     => 'service/imode/make/content/spec/screen_area/index.html',
        'appli'       => 'service/imode/make/content/spec/iappli/index.html',
        'flash'       => 'service/imode/make/content/spec/flash/index.html',
        'frame'       => 'service/imode/make/content/spec/frame_size/index.html',
        'decomail'    => 'binary/pdf/service/imode/make/content/spec/imode_spec.pdf',
        'spec'        => 'binary/pdf/service/imode/make/content/spec/imode_spec.pdf',
/*         'sr_anime' => array( */
/*         ), */
/*         'melody' => array( */
/*         ), */
/*         'voice' => array( */
/*         ), */
/*         'movie' => array( */
/*         ), */
    );

    var $info_prop_list = array(
        'device',
        'model',
        'machiuke_w',
        'machiuke_h',
        //'browser_w',
        //'browser_h',
        //'color_num',
    );

    var $imode_spec = null;


    function _parseProfileInfo_Useragent($content)
    {
        $encoding = mb_detect_encoding($content, 'ASCII,JIS,UTF-8,EUC-JP,SJIS');
        $content  = mb_convert_encoding($content, 'UTF-8', $encoding);
        $content  = mb_convert_kana($content, 'a');


        $replace_list = array(
            '(\n|\r|<BR[^>]*>)' => '',
            '<span[^>]*>'       => '',
            '</span>'           => '',
            '<div[^>]*>'        => '',
            '</div>'            => '',
            '<font[^>]*>'       => '',
            '</font>'           => '',
            '&nbsp;'            => ' ',
        );
        foreach ($replace_list as $pattern => $replacement) {
            $content = mb_eregi_replace($pattern, $replacement, $content, 'i');
        }

        $reg  = '';
        $reg .= '(<A NAME="p\d+">iモード対応HTML([\d\.]+)[^>]*</A>)';
        $reg .= '|';
        $reg .= '(';
        $reg .= '<TD[^>]*>\s*([a-z]+\d+[\w&;\+]*)(\s*\(([^\)]+)\))?(<a[^>]*><img[^>]*>\d*</a>)?</TD>';
        $reg .= '((<td[^>]*>([^<]*|<a[^>]*>|</a>|<img[^>]*>)*</td>)+)';
        $reg .= ')';
        mb_ereg_search_init($content, $reg, 'i');

        // 検索実行
        $html = '';
        $regex_result = array();
        while ($ret = mb_ereg_search_regs()) {
            if (!empty($ret[2])) {
                $html = $ret[2];
            } else {
                $regex_result[] = array(
                    'device' => $ret[4],
                    'model'  => $ret[6],
                    'html'   => $html,
                    'ua'     => $ret[8],
                );
            }
        }

        $generation_list = array(
            '1.0' => 'mova',
            '2.0' => 'FOMA',
        );

        foreach ($regex_result as $row) {
            $device = $this->_normalizeDeviceName($row['device']);
            $info   = & $this->_getProfileInfo($device);

            // 機種名
            $info->setDeviceID($device);

            // モデル名
            $model = $row['model'];
            if (empty($model)) {
                $model = $this->_normalizeModelName($row['device']);
            }
            $info->setModel($model);

            // ユーザエージェント
            $generation = '1.0';
            $cache = 5;
            $reg = 'DoCoMo/([\d\.]+)(\s|/)[\w\+]+((/|\()c(\d+))?';
            mb_ereg_search_init($row['ua'], $reg, 'i');
            while($ret = mb_ereg_search_regs()) {
                if ((int)$generation < (int)$ret[1]){
                    $generation = $ret[1];
                }
                if ($cache < $ret[5]) {
                    $cache = (int)$ret[5];
                }
            }
            $generation = $generation_list[$generation];

            // 世代
            $info->set('generation', $generation);
            // HTMLバージョン
            $info->set('browser', 'html', $row['html']);
            // キャッシュサイズ
            $info->set('browser', 'cache', $cache);
        }
    }

    function _parseProfileInfo_Display($content)
    {
        $encoding = mb_detect_encoding($content, 'ASCII,JIS,UTF-8,EUC-JP,SJIS');
        $content  = mb_convert_encoding($content, 'UTF-8', $encoding);
        $content  = mb_convert_kana($content, 'a');

        $replace_list = array(
            '(\n|\r|<BR[^>]*>)' => '',
            '<span[^>]*>'       => '',
            '</span>'           => '',
            '<font[^>]*>'       => '',
            '</font>'           => '',
        );
        foreach ($replace_list as $pattern => $replacement) {
            $content = mb_eregi_replace($pattern, $replacement, $content, 'i');
        }

        $reg  = '';
        $reg .= '<TD>([a-z]+\d+[\w&;\+]*)(\(([^\)]+)\)|)</TD>';
        $reg .= '<TD>([^>]*)</TD>';
        $reg .= '<TD>([^>]*)</TD>';
        $reg .= '<TD>([^>]*)</TD>';
        $reg .= '<TD>([^>]*)([^>]*|<IMG[^>]*>)</TD>';
        $reg .= '<TD>([^>\d]*)(\d+)[^>]*</TD>';
        mb_ereg_search_init($content, $reg, 'i');

        // 検索実行
        $regex_result = array();
        while ($ret = mb_ereg_search_regs()) {
            $regex_result[] = array(
                'device'     => $ret[1],
                'model'      => $ret[3],
                'font'       => $ret[4],
                'char'       => $ret[5],
                'browser'    => $ret[6],
                'machiuke'   => $ret[7],
                'color_type' => $ret[9],
                'color_num'  => $ret[10],
            );
        }

        foreach ($regex_result as $row) {
            $device = $this->_normalizeDeviceName($row['device']);
            $info = & $this->_getProfileInfo($device);

            // 機種名
            $info->setDeviceID($device);

            // モデル名
            $model = $row['model'];
            if (empty($model)) {
                $model = $this->_normalizeModelName($row['device']);
            }
            $info->setModel($model);

            // フォントサイズ
            $reg = '(\(([^\d]+)\))([^\(]+)';
            mb_ereg_search_init($row['font'], $reg, 'i');
            $fonts = array();
            while ($ret = mb_ereg_search_regs()) {
                $fonts[$ret[2]] = $ret[3];
            }
            if (empty($fonts)) {
                $fonts = array($row['font']);
            }

            foreach ($fonts as $key => $value) {
                $size = mb_split('[^\d]+', $value);
                $fonts[$key] = array(
                    'w' => $size[0],
                    'h' => $size[1],
                );
            }
            $info->set('display', 'font', $fonts);

            // 表示文字数
            $reg = '(\(([^\d]+)\))([^\(]+)';
            mb_ereg_search_init($row['char'], $reg, 'i');
            $chars = array();
            while ($ret = mb_ereg_search_regs()) {
                $chars[$ret[2]] = $ret[3];
            }
            $info->set('display', 'char', empty($chars) ? $row['char'] : $chars);

            // ブラウザ表示領域
            $browsers = array();
            if ($row['browser']{0} === '(') {
                $reg = '(\(([^\d]+)\)) *([^\(]+)';
                mb_ereg_search_init($row['browser'], $reg, 'i');
                while ($ret = mb_ereg_search_regs()) {
                    $browsers[$ret[2]] = $ret[3];
                }
            } else {
                $reg = '([^(]+)(\(([^\d]*)\))';
                mb_ereg_search_init($row['browser'], $reg, 'i');
                while ($ret = mb_ereg_search_regs()) {
                    $browsers[$ret[3]] = $ret[1];
                }
                if (count($browsers) < 2) {
                    $reg = '(\(([^\d]+)\))([^\(]+)';
                    mb_ereg_search_init($row['browser'], $reg, 'i');
                    $browsers = array();
                    while ($ret = mb_ereg_search_regs()) {
                        $browsers[$ret[3]] = $ret[1];
                    }
                    if (empty($browsers)) {
                        $browsers = array($row['browser']);
                    }
                }
            }

            foreach ($browsers as $key => $value) {
                $size = mb_split('[^\d]+', $value);
                $browsers[$key] = array(
                    'w' => $size[0],
                    'h' => $size[1],
                );
            }
            $info->set('display', 'browser', $browsers);

            // 待ち受け表示領域
            $reg = '(\(([^\d]+)\)) *([^\(]+)';
            mb_ereg_search_init($row['machiuke'], $reg, 'i');
            $machiukes = array();
            while ($ret = mb_ereg_search_regs()) {
                $machiukes[$ret[2]] = $ret[3];
            }
            if (empty($machiukes)) {
                $reg = '^([^\d]+)(.*)';
                $ret = array();
                mb_eregi($reg, $row['machiuke'], $ret);
                if (empty($ret)) {
                    $machiukes = array($row['machiuke']);
                } else {
                    $machiukes = array(
                        $ret[1] => $ret[2],
                    );
                }
            }
            foreach ($machiukes as $key => $value) {
                $size = mb_split('[^\d]+', $value);
                if (count($size) >= 2) {
                    $machiukes[$key] = array(
                        'w' => $size[0],
                        'h' => $size[1],
                    );
                }
            }
            $info->set('display', 'machiuke', $machiukes);

            // カラー
            $info->set('display', 'color', array(
                           'type' => $row['color_type'],
                           'num' => $row['color_num'],
                       ));
        }
    }

    function _parseProfileInfo_Appli($content)
    {
        $encoding = mb_detect_encoding($content, 'ASCII,JIS,UTF-8,EUC-JP,SJIS');
        $content  = mb_convert_encoding($content, 'UTF-8', $encoding);
        $content  = mb_convert_kana($content, 'a');

        $replace_list = array(
            '(\n|\r|<BR[^>]*>)' => '',
            '<span[^>]*>'       => '',
            '</span>'           => '',
            '<font[^>]*>'       => '',
            '</font>'           => '',
        );
        foreach ($replace_list as $pattern => $replacement) {
            $content = mb_eregi_replace($pattern, $replacement, $content, 'i');
        }

        $reg  = '';
        $reg .= '(<TD[^>]*>DoJa-([.\da-z]+)[^>]+</TD>)?';
        $reg .= '<TD>([a-z]+\d+[\w&;\+]*)(\(([^\)]+)\)|)</TD>';
        $reg .= '<TD>(\d+)/?(\d*)?</TD>';
        $reg .= '<TD>(\d+)[^\d]+(\d+)(<A[^>]*><IMG[^>]*>\d*</A>)?</TD>';
        $reg .= '<TD>(\d+)[^\d]+(\d+)(<A[^>]*><IMG[^>]*>\d*</A>)?</TD>';
        $reg .= '<TD>(\d+)/?(\d*)</TD>';
        $reg .= '<TD>(\d+)[^\d]+(\d+)</TD>';
        mb_ereg_search_init($content, $reg, 'i');

        // 検索実行
        $regex_result = array();
        while ($ret = mb_ereg_search_regs()) {
            $regex_result[] = array(
                'doja'                => $ret[2],
                'device'              => $ret[3],
                'model'               => $ret[5],
                'applisize_jar'       => $ret[6],
                'applisize_scratchpad'=> $ret[7],
                'drawarea_panel_w'    => $ret[8],
                'drawarea_panel_h'    => $ret[9],
                'drawarea_canvas_w'   => $ret[11],
                'drawarea_canvas_h'   => $ret[12],
                'heap_java'           => $ret[14],
                'heap_native'         => $ret[15],
                'font_w'              => $ret[16],
                'font_h'              => $ret[17],
            );
        }

        $doja = '';
        foreach ($regex_result as $row) {
            $device = $this->_normalizeDeviceName($row['device']);
            $info = & $this->_getProfileInfo($device);

            if (!empty($row['doja'])) {
                $doja = $row['doja'];
            }

            // 機種名
            $info->setDeviceID($device);

            // モデル名
            $model = $row['model'];
            if (empty($model)) {
                $model = $this->_normalizeModelName($row['device']);
            }
            $info->setModel($model);

            // DoJa
            $info->set('appli', 'doja', $doja);

            // アプリサイズ
            if (empty($row['applisize_jar'])) {
                $info->set('appli', 'applisize', $row['applisize_jad']);
            } else {
                $info->set('appli', 'applisize', array(
                               'jar'        => $row['applisize_jar'],
                               'scratchpad' => $row['applisize_scratchpad'],
                           ));
            }

            // 描画領域
            $info->set('appli', 'drawarea', array(
                           'panel' => array(
                               'w' => $row['drawarea_panel_w'],
                               'h' => $row['drawarea_panel_h'],
                           ),
                           'canvas' => array(
                               'w' => $row['drawarea_canvas_w'],
                               'h' => $row['drawarea_canvas_h'],
                           ),
                       ));

            // ヒープ容量
            if (empty($row['heap_native'])) {
                $info->set('appli', 'heap', $row['heap_java']);
            } else {
                $info->set('appli', 'heap', array(
                               'java' => $row['heap_java'],
                               'native' => $row['heap_native'],
                           ));
            }

            // フォントサイズ
            $info->set('appli', 'font', array(
                           'w' => $row['font_w'],
                           'h' => $row['font_h'],
                       ));
        }
    }

    function _parseProfileInfo_Flash($content)
    {
        $encoding = mb_detect_encoding($content, 'ASCII,JIS,UTF-8,EUC-JP,SJIS');
        $content  = mb_convert_encoding($content, 'UTF-8', $encoding);
        $content  = mb_convert_kana($content, 'a');

        $replace_list = array(
            '(\n|\r|<BR[^>]*>)' => '',
            '<span[^>]*>'       => '',
            '</span>'           => '',
            '<font[^>]*>'       => '',
            '</font>'           => '',
        );
        foreach ($replace_list as $pattern => $replacement) {
            $content = mb_eregi_replace($pattern, $replacement, $content, 'i');
        }



        $reg  = '';
        $reg .= '(<A NAME="p\d+">(Flash) ([a-z\d\. ]+)</A>)';
        $reg .= '|';
        $reg .= '(';
        $reg .= '<TD[^>]*>\s*([a-z]+\d+[\w&;\+]*)(\(([^\)]+)\))?\s*</TD>';
        $reg .= '<TD[^>]*>\s*([^>]*)\s*</TD>';
        $reg .= '<TD[^>]*>\s*([^>]*)(<img[^>]*alt="([^"]+)"[^>]*>)?\s*</TD>';
        $reg .= '<TD[^>]*>\s*(\d+)</TD>';
        $reg .= '<TD[^>]*>\s*([^>]*)(<A[^>]*><img[^>]*>\d*</A>)?\s*</TD>';
        $reg .= '<TD[^>]*>\s*<img[^>]*alt="([^"]+)"[^>]*>\s*</TD>';
        $reg .= '<TD[^>]*>\s*(\d+)\s*</TD>';
        $reg .= ')';
        mb_ereg_search_init($content, $reg, 'i');

        // 検索実行
        $flash = '';
        $regex_result = array();
        while ($ret = mb_ereg_search_regs()) {
            if ($ret[2] === 'Flash') {
                $flash = $ret[3];
            } else {
                $regex_result[] = array(
                    'flash'    => $flash,
                    'device'   => $ret[5],
                    'model'    => $ret[7],
                    'browser'  => $ret[8],
                    'machiuke' => $ret[9],
                    'memory'   => $ret[12],
                    'font'     => $ret[13],
                    'pointing' => $ret[15],
                    'inline'   => $ret[16],
                );
            }
        }

        foreach ($regex_result as $row) {
            $device = $this->_normalizeDeviceName($row['device']);
            $info = & $this->_getProfileInfo($device);

            // 機種名
            $info->setDeviceID($device);

            // モデル名
            $model = $row['model'];
            if (empty($model)) {
                $model = $this->_normalizeModelName($row['device']);
            }
            $info->setModel($model);

            // Flashバージョン
            $info->set('flash', 'flash', $row['flash']);

            // ブラウザ描画領域
            $browsers = $this->_parseDefineList_size($this->_parseDefineList($row['browser']));
            $info->set('flash', 'browser', $browsers);

            // 待ち受け描画領域
            $machiukes = $this->_parseDefineList_size($this->_parseDefineList($row['machiuke']));
            $info->set('flash', 'machiuke', $machiukes);

            // メモリ
            $info->set('flash', 'memory', $row['memory']);

            // フォント
            if (mb_ereg_match('^\d{1,2}[^\d]+\d{1,2}[^\d]+\d{1,2}[^\d]+\d{1,2}$', $row['font'])) {
                $info->set('flash', 'font', $row['font']);
            } else {
                $fonts = array();
                preg_match_all('/\d{1,2}[^\d]+\d{1,2}/', $row['font'], $fonts);
                $info->set('flash', 'font', $fonts);
            }

            // ポインティングデバイス
            $info->set('flash', 'pointing', $row['pointing']);

            // インライン再生数
            $info->set('flash', 'inline', $row['inline']);
        }
    }

    function _parseProfileInfo_Frame($content)
    {
        $encoding = mb_detect_encoding($content, 'ASCII,JIS,UTF-8,EUC-JP,SJIS');
        $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        $content = mb_convert_kana($content, 'a');

        $replace_list = array(
            '(\n|\r|<BR[^>]*>)' => '',
            '<span[^>]*>'       => '',
            '</span>'           => '',
            '<font[^>]*>'       => '',
            '</font>'           => '',
        );
        foreach ($replace_list as $pattern => $replacement) {
            $content = mb_eregi_replace($pattern, $replacement, $content, 'i');
        }

        $reg  = '';
        $reg .= '<TD[^>]*>([a-z]+\d+[\w&;\+]*)(\(([^\)]+)\))?</TD>';
        $reg .= '((';
        $reg .= '(</tr><tr[^>]*>[^<]*)?';
        $reg .= '(<TD[^>]*>\s*\d+[^\d]+\d+\s*</TD>)+';
        $reg .= ')+)';
        mb_ereg_search_init($content, $reg, 'i');

        // 検索実行
        $regex_result = array();
        while ($ret = mb_ereg_search_regs()) {
            $regex_result[] = array(
                'device' => $ret[1],
                'model'  => $ret[3],
                'size'   => $ret[4],
            );
        }

        foreach ($regex_result as $row) {
            $device = $this->_normalizeDeviceName($row['device']);
            $info = & $this->_getProfileInfo($device);

            // 機種名
            $info->setDeviceID($device);

            // モデル名
            $model = $row['model'];
            if (empty($model)) {
                $model = $this->_normalizeModelName($row['device']);
            }
            $info->setModel($model);

            // サイズ
            $matches = array();
            preg_match_all('/(\d+)[^\d]+(\d+)/', $row['size'], $matches);
            $size = array();
            for ($i = 0; $i < count($matches[0]); $i++) {
                $size[] = array(
                    'w' => $matches[1][$i],
                    'h' => $matches[2][$i],
                );
            }
            $info->set('frame', 'size', $size);
        }
    }

    function _parseProfileInfo_Decomail($content)
    {
        $regex_result = $this->_parseImodeSpec($content);


        foreach ($regex_result as $row) {
            $device = $this->_normalizeDeviceName($row['device']);
            $info = & $this->_getProfileInfo($device);

            // 機種名
            $info->setDeviceID($device);

            // モデル名
            $model = $row['model'];
            if (empty($model)) {
                $model = $this->_normalizeModelName($row['device']);
            }
            $info->setModel($model);

            // デコメール
            $decomail = $row['contents']['decomail'];
            if (!empty($decomail)) {
                $decomail = mb_split('\*', $decomail);
                $version = $decomail[0];
                $deco_tpl = '対応';
                if (isset($decomail[1])) {
                    switch ($decomail[1]) {
                    case 1:
                        $deco_tpl = '非対応';
                        break;
                    case 2:
                        $deco_tpl = '非対応';
                        break;
                    case 3:
                        $deco_tpl = 'DL非対応';
                        break;
                    case 4:
                        break;
                    }
                }
                $info->set('decomail', 'version', $version);
                $info->set('decomail', 'template', $deco_tpl);

                // デコアニメ
                $decoanime = $row['contents']['decoanime'];
                if (!empty($decoanime)) {
                    $deco_anime = '対応';
                } else {
                    $deco_anime = '非対応';
                }

                $info->set('decomail', 'decoanime', $deco_anime);
            }
        }
    }

    function _parseProfileInfo_Spec($content)
    {
        $regex_result = $this->_parseImodeSpec($content);

        foreach ($regex_result as $row) {
            $device = $this->_normalizeDeviceName($row['device']);
            $info = & $this->_getProfileInfo($device);

            // 機種名
            $info->setDeviceID($device);

            // モデル名
            $model = $row['model'];
            if (empty($model)) {
                $model = $this->_normalizeModelName($row['device']);
            }
            $info->setModel($model);

            foreach ($row['contents'] as $key => $val) {
                $info->set('spec', $key, $val);
            }
        }
    }

    function _normalizeDeviceName($device)
    {
        $device = html_entity_decode($device, ENT_NOQUOTES, 'UTF-8');
        $device = htmlentities($device, ENT_NOQUOTES, 'UTF-8');
        $device = mb_ereg_replace('&mu;', 'myu', $device);
        $device = mb_ereg_replace('III', '3', $device);
        $device = mb_ereg_replace('II', '2', $device);

        return $device;
    }

    function _normalizeModelName($model)
    {
        $model = html_entity_decode($model, ENT_NOQUOTES, 'UTF-8');

        return $model;
    }

    function _parseDefineList($value)
    {
        $result = array();
        if ($value{0} === '(') {
            $reg = '(\(([^\d]+)\)) *([^\(]+)';
            mb_ereg_search_init($value, $reg, 'i');
            while ($ret = mb_ereg_search_regs()) {
                $result[$ret[2]] = $ret[3];
            }
        } else {
            $reg = '([^(]+)(\(([^\d]*)\))';
            mb_ereg_search_init($value, $reg, 'i');
            while ($ret = mb_ereg_search_regs()) {
                $result[$ret[3]] = $ret[1];
            }
            if (count($result) < 2) {
                $reg = '(\(([^\d]+)\))([^\(]+)';
                mb_ereg_search_init($value, $reg, 'i');
                $result = array();
                while ($ret = mb_ereg_search_regs()) {
                    $result[$ret[3]] = $ret[1];
                }
                if (empty($result)) {
                    $result = array($value);
                }
            }
        }

        return $result;
    }

    function _parseDefineList_size($list)
    {
        $result = array();
        foreach ($list as $key => $value) {
            $size = mb_split('[^\d]+', $value);
            if (count($size) >= 2) {
                $result[$key] = array(
                    'w' => $size[0],
                    'h' => $size[1],
                );
            }
        }

        return $result;
    }

    function _parseImodeSpec($content)
    {
        if (!is_null($this->imode_spec)) {
            return $this->imode_spec;
        }


        $tmpfile = tempnam(sys_get_temp_dir(), '');
        $outfile = "{$tmpfile}.txt";

        file_put_contents($tmpfile, $content);

        exec("pdftotext -raw -nopgbrk -eol unix {$tmpfile} {$outfile}");

        $contents_type = array(
            'html',
            'xhtml',
            'iappli',
            'felica',
            'flash',
            'imotion',
            'vlive',
            'pdf',
            'charaden',
            'decomail',
            'decoanime',
            'toruca',
            'ichannel',
            'emoji',
            'gps',
            'frame',
            'menuicon',
            'kisekae',
            'machichara',
            'iconcierge',
            'barcode',
            'ssl',
            'drm',
            'etc',
        );

        $fp = fopen($outfile, 'r');
        $this->imode_spec = array();


        $tmp = '';
        $lines = array();
        $index = 0;
        while($line = fgets($fp, 1024)) {
            $line = mb_ereg_replace('\r\n|\r|\n', '', $line);
            $lines[] = $line;
        }
        fclose($fp);

        unlink($outfile);
        unlink($tmpfile);


        $data_list = array();
        for ($i = 0; $i < count($lines); $i++) {
            if (mb_ereg_match('^\*\d+$', $lines[$i])) {
                $data_list[$i-1] = $lines[$i-1].$lines[$i].' '.$lines[$i+1];
                $i++;
            } elseif (mb_ereg_match('^.*\*\d+$', $lines[$i])) {
                $data_list[$i] = $lines[$i].' '.$lines[$i+1];
                $i++;
            } else {
                $data_list[$i] = $lines[$i];
            }
        }


        $reg  = '';
        $reg .= '([A-Z]{1,2}\d{3,4}[\w&;\+]*[^ \(\s]*)\s*(\(([^\)]+)\))?';
        $reg .= '\s*(.*)';

        foreach ($data_list as $data) {
            if (preg_match_all("/{$reg}$/", $data, $match)) {
                $tmp = array_pad(mb_split(' ', $match[4][0]), count($contents_type), '');
                $spec = array_combine($contents_type, $tmp);
                $this->imode_spec[] = array(
                    'device'   => $match[1][0],
                    'model'    => $match[3][0],
                    'contents' => $spec,
                );
            }
        }

        return $this->imode_spec;
    }
}
