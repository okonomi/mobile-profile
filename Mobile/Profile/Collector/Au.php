<?php
require_once dirname(__FILE__) . '.php';


class Mobile_Profile_Collector_Au extends Mobile_Profile_Collector
{
    var $base_url = '';

    var $url_list = array (
        'id'      => 'http://www.au.kddi.com/ezfactory/tec/spec/4_4.html',
        'basic'   => 'http://www.au.kddi.com/ezfactory/tec/spec/new_win/ezkishu.html',
/*         'brew' => array( */
/*             'http://www.au.kddi.com/ezfactory/service/brew.html', */
/*         ), */
/*         'java' => array( */
/*             'http://www.au.kddi.com/ezfactory/tec/spec/ezplus.html', */
/*         ), */
        'decomail' => 'http://www.au.kddi.com/cgi-bin/modellist/allList.cgi?ServiceID=106',
    );

    var $info_prop_list = array(
        'device',
        'model',
        'browser_w',
        'browser_h',
        'appli',
        'flash_ver',
    );

    var $device_map = null;

    var $device_model_jointed = array ();

    var $device_code = null;


    function Model_Profile_Carrier_Au()
    {
    }

    function _parseProfileInfo_Id($content)
    {
        $xml = simplexml_import_dom(DOMDocument::loadHTML($content));

        $count = 0;
        $info = null;
        $elem = $xml->xpath('//table[@cellpadding="0"]/tr[@bgcolor="#ffffff"]/td/div[@class="TableText"]');
        foreach ($elem as $val) {
            $text = (string)$val;

            if (empty($text)) {
                continue;
            }

            if ($count % 2 == 0) {
                $model = $this->_normalizeModelName($text);
                $info = & $this->_getProfileInfo($model);

                $info->setModel($text);
            } else {
                $info->setDeviceID($text);

                if (preg_match('/^[A-Z]{2}2\w/', $text)) {
                    if (preg_match('/^C\d+.*/', $info->get('model'))) {
                        $info->set('generation', 'cdmaOne');
                    } else {
                        $info->set('generation', '1X');
                    }
                } else {
                    $info->set('generation', 'WIN');
                }
            }

            $count++;
        }
    }

    function _parseProfileInfo_Basic($content)
    {
        $flash_str = array(
            "●" => '2.0',
            "◎" => '1.1',
            "○" => '1.1',
            "－" => '',
        );


        $matches = array();
        $xml   = simplexml_import_dom(DOMDocument::loadHTML($content));
        $elems = $xml->xpath('//table[@width="892"]/tr[@bgcolor="#ffffff"]');
        foreach ($elems as $elem) {
            $row = array(
                'model'           => (string)$elem->td[0]->div,
                'browser_version' => (string)$elem->td[1]->div,
                'display_color'   => (string)$elem->td[2]->div,
                'browser_size'    => (string)$elem->td[4]->div,
                'display_size'    => (string)$elem->td[5]->div,
                'flash_version'   => (string)$elem->td[11]->div,
            );
            // モデル名
            $model = $this->_normalizeModelName($row['model']);
            $info  = & $this->_getProfileInfo($model);

            // ディスプレイ
            if (preg_match('/(カラー)\((.+)色\)/', $row['display_color'], $matches)) {
                $info->set('display', 'color', array('type' => $matches[1], 'num' => Mobile_Profile_Collector_Au::toInt($matches[2])));
            }
            if (preg_match('/(\d+)×(\d+)/', $row['display_size'], $matches)) {
                $info->set('display', 'width',  $matches[1]);
                $info->set('display', 'height', $matches[2]);
            }

            // ブラウザ
            $info->set('browser', 'version', $row['browser_version']);
            if (preg_match('/(\d+)×(\d+)/', $row['browser_size'], $matches)) {
                $info->set('browser', 'width',  $matches[1]);
                $info->set('browser', 'height', $matches[1]);
            }

            // Flashバージョン
            $tmp = $row['flash_version'];
            if (isset($flash_str[$tmp])) {
                $flash_ver = $flash_str[$tmp];
            } else {
                $flash_ver = '';
            }
            $info->set('flash', 'flash', $flash_ver);
        }
    }

    function _parseProfileInfo_Brew($content)
    {
        $content = mb_eregi_replace('[\r\n\t]', '', $content);

        $reg  = '';
        $reg .= '<td><div class="TableText">([^>]+)</div></td>';
        $reg .= '<td align="center"><div class="TableText">([\d\.]+)</div></td>';

        mb_ereg_search_init($content, $reg, 'i');

        // 検索実行
        while ($ret = mb_ereg_search_regs()) {
            $model = $this->_normalizeModelName($ret[1]);
            $model = mb_ereg_replace(' \((.*)\)', '\1', $model);
            $brew  = 'BREW'.$ret[2];

            $info = & $this->_getProfileInfo($model);

            $info->set('appli', $brew);
        }
    }

    function _parseProfileInfo_Java($content)
    {
        $content = mb_eregi_replace('[\r\n\t]', '', $content);

        $reg  = '';
        $reg .= '<td bgcolor="#f2f2f2" nowrap><div class="TableText">(Phase[\d\.]+)</div></td>';
        $reg .= '<td><div class="TableText">au<br>([^>]+)</div></td>';

        mb_ereg_search_init($content, $reg, 'i');

        // 検索実行
        while ($ret = mb_ereg_search_regs()) {
            $phase = $ret[1];
            $models = explode(' / ', $ret[2]);

            foreach ($models as $model) {
                $model = mb_ereg_replace('\(.*\)', '', $model);
                $model = trim($model);

                $row = & $result[$model];

                $row['appli'] = $phase;
            }
        }
    }

    function _parseProfileInfo_Decomail($content)
    {
        $xml = simplexml_import_dom(DOMDocument::loadHTML($content));

        $elem = $xml->xpath('//div[@id="primaryArea"]/table[@class="table middle"]/tr/td[@bgcolor="#f1f4f6"]');
        $text = (string)current($elem);

        $values = mb_split(", ", $text);

        mb_regex_encoding('UTF-8');
        foreach ($values as $model) {
            $model = $this->_normalizeModelName($model);

            // 機種名だけをがんばってとりだす
            if (preg_match('/[^ a-zA-Z0-9\/]+/', $model)) {
                $tokens = mb_split(' ', $model);
                $model = end($tokens);
            }

            $info =& $this->_getProfileInfo($model, false);
            if (is_null($info)) {
                continue;
            }


            $info->set('decomail', 'allow', 't');
        }
    }

    function _normalizeModelName($model)
    {
        $model = mb_ereg_replace('\/.*', '', $model);
        $model = trim($model);

        return $model;
    }

    function toInt($value)
    {
        $value = str_replace(',', '', $value);

        switch ($value) {
        case "26万":
            $value = 260000;
            break;
        case "6万5千":
        case "6.5万":
            $value = 65000;
            break;
        case "6万":
            $value = 60000;
            break;
        default:
            $value = (int)$value;
        }

        return $value;
    }
}
