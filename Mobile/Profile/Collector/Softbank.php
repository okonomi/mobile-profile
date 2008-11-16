<?php
require_once dirname(__FILE__) . '.php';

class Mobile_Profile_Collector_Softbank extends Mobile_Profile_Collector
{
    var $base_url = '';

    var $url_list = array (
/*         'decomail' => 'http://mb.softbank.jp/mb/service/3G/mail/arrange/', */
/*         'kisekae'  => 'http://mb.softbank.jp/mb/service/3G/contents/change_arrange/', */
    );


    function _correctProfileInfo()
    {
        // 機種情報
        $csv   = $this->_getDeviceSpecList();
        $csv   = mb_convert_encoding($csv, 'UTF-8', 'sjis-win');
        $specs = $this->explodeCSV($csv);

//        print_r($specs);
        unset($specs[0]);


        // サービス対応情報
        $allows = $this->_getAllowList();

        $this->_parseProfileInfo_Id($specs);
        $this->_parseProfileInfo_Display($specs);
        $this->_parseProfileInfo_Browser($specs);
        $this->_parseProfileInfo_Flash($specs);

        $this->_parseProfileInfo_Decomail($allows);
        $this->_parseProfileInfo_Kisekae($allows);

        parent::_correctProfileInfo();
    }

    function _getDeviceSpecList()
    {
        $request = new HTTP_Request();


        // ログインページをGET
        $request->reset('https://creation.mb.softbank.jp/members/mem_login.html');
        $request->setMethod(HTTP_REQUEST_METHOD_GET);
        if (PEAR::isError($ret = $request->sendRequest())) {
            exit($ret->getMessage());
        }
        // セッションIDらしきものが発行される
        $cookie = $request->getResponseCookies();
        $id = $cookie[0];


        // ログイン
        $request->reset('https://creation.mb.softbank.jp/members/mem_login.html');
        $request->setMethod(HTTP_REQUEST_METHOD_POST);
        $request->addPostData('email',     $this->options['username']);
        $request->addPostData('pass_text', $this->options['password']);
        $request->addPostData('save',      '1');
        $request->addCookie($id['name'], $id['value']);
        if (PEAR::isError($ret = $request->sendRequest())) {
            exit($ret->getMessage());
        }
        $cookie = $request->getResponseCookies();


        // スペック一覧ダウンロード
        $request->reset('http://creation.mb.softbank.jp/terminal/spec_download.html');
        $request->setMethod(HTTP_REQUEST_METHOD_POST);
        $request->addPostData('lup', 'y');
        $request->addCookie($id['name'], $id['value']);
        foreach ($cookie as $c) {
            $request->addCookie($c['name'], $c['value']);
        }
        if (PEAR::isError($ret = $request->sendRequest())) {
            exit($ret->getMessage());
        }
        $csv = $request->getResponseBody();


        // ログアウト
        $request->reset('http://creation.mb.softbank.jp/members/mem_logout.html');
        $request->setMethod(HTTP_REQUEST_METHOD_GET);
        $request->addCookie($id['name'], $id['value']);
        if (PEAR::isError($ret = $request->sendRequest())) {
            exit($ret->getMessage());
        }


        return $csv;
    }

    function _getAllowList()
    {
        // 機種一覧
        $request = new HTTP_Request('http://mb.softbank.jp/mb/shared/xml/hub/product/models.xml');
        if (PEAR::isError($ret = $request->sendRequest())) {
            exit($ret->getMessage());
        }
        $content = $request->getResponseBody();
        $xml = simplexml_load_string($content);

        $models = array();
        foreach ($xml->item as $item) {
            $models[(string)$item['id']] = (string)$item;
        }


        // サービス/機種対応一覧
        $request = new HTTP_Request('http://mb.softbank.jp/mb/shared/xml/hub/service/models.xml');
        if (PEAR::isError($ret = $request->sendRequest())) {
            exit($ret->getMessage());
        }
        $content = $request->getResponseBody();
        $xml = simplexml_load_string($content);

        $allows = array();
        foreach ($xml->item as $item) {
            $model_id_list = explode(',', $item);
            foreach ($model_id_list as $id) {
                if (isset($models[$id])) {
                    $allows[(string)$item['id']][] = $models[$id];
                } else {
                    $allows[(string)$item['id']][] = $id;
                }
            }
        }

        return $allows;
    }

    function _parseProfileInfo_Id($content)
    {
        foreach ($content as $line) {
            $id   = $this->_normalizeDeviceName($line[9]);
            $info = & $this->_getProfileInfo($id);

            $model = $this->_normalizeModelName($line[0]);

            $info->set('device',     $id);
            $info->set('model',      $model);
            $info->set('generation', $line[2]);
        }
    }

    function _parseProfileInfo_Display($content)
    {
        foreach ($content as $line) {
            $id   = $this->_normalizeDeviceName($line[9]);
            $info = & $this->_getProfileInfo($id);


            list($w, $h) = explode('*', $line[10]);
            $info->set('display', 'width',  $w);
            $info->set('display', 'height', $h);

            $matches = array();
            if (preg_match('/^(G|C)(\d+)/', $line[11], $matches)) {
                $info->set('display', 'color', array('type' => $matches[1], 'num' => $matches[2]));
            }
        }
    }

    function _parseProfileInfo_Browser($content)
    {
        foreach ($content as $line) {
            $id   = $this->_normalizeDeviceName($line[9]);
            $info = & $this->_getProfileInfo($id);


            $browser_size_list = array();
            $size_list = mb_split("\r\n|\r|\n", $line[23]);
            foreach ($size_list as $size) {
                if (preg_match('/((.*)：)?(\d+) x (\d+)/', $size, $matches)) {
                    $tmp = array(
                        'width'  => $matches[3],
                        'height' => $matches[4],
                    );

                    if (empty($matches[2])) {
                        $browser_size_list[] = $tmp;
                    } else {
                        $browser_size_list[$matches[2]] = $tmp;
                    }
                }
            }
            $info->set('browser', 'size', $browser_size_list);
        }
    }

    function _parseProfileInfo_Flash($content)
    {
        foreach ($content as $line) {
            $id   = $this->_normalizeDeviceName($line[9]);
            $info = & $this->_getProfileInfo($id);

            if (preg_match('/^Flash Lite\[TM\]([\d\.]+)/', $line[18], $matches)) {
                $info->set('flash', 'flash', $matches[1]);
            }
        }
    }

    function _parseProfileInfo_Appli($content, & $result)
    {
        $content = mb_eregi_replace('[\f\r\n\t]', '', $content);
        $content = mb_eregi_replace('<font[^>]*>', '', $content);
        $content = mb_eregi_replace('</font>', '', $content);
        $content = mb_eregi_replace('<br>', '', $content);
        $content = mb_eregi_replace('noerap', 'nowrap', $content);
        $content = mb_eregi_replace('(<td valign="center" nowrap bgcolor="#ffffff">[\w\.\-]+</td>){4}</tr>', '</tr>', $content);
        
        $reg  = '';
        $reg .= '<tr>';
        $reg .= '<td[^>]*bgcolor="#ffffff"[^>]*>([\w\- '."\xad\xb6".']+)[\w\(\) ]*</td>';
        $reg .= '(<td[^>]*bgcolor="#ffffff"[^>]*>([\w\.\-]+)</td>){0,1}';
        $reg .= '(<td[^>]*bgcolor="#ffffff"[^>]*>([\w\.\-]+)</td>){0,1}';
        $reg .= '(<td[^>]*bgcolor="#ffffff"[^>]*>([\w\.\-]+)</td>){0,1}';
        $reg .= '.*?';
        $reg .= '</tr>';
        
         mb_ereg_search_init($content, $reg, 'i');

        // 検索実行
        $jscl = '';
        $cldc = '';
        $midp = '';
        while ($ret = mb_ereg_search_regs()) {
            if (!empty($ret[3])) {
                $jscl = $ret[3];
            }
            if (!empty($ret[5])) {
                $cldc = $ret[5];
            }
            if (!empty($ret[7])) {
                $midp = $ret[7];
            }
            
            $id = $this->_normalizeModelName($ret[1], $result);
            
            $row = & $result[$id];
            
            $row['model'] = $id;
            $row['midp']  = $midp;
            $row['cldc']  = $cldc;
            $row['jscl']  = $jscl;
        }
    }

    function _parseProfileInfo_Decomail($allows)
    {
        $request = new HTTP_Request('http://mb.softbank.jp/mb/service/3G/mail/arrange/');
        if (PEAR::isError($ret = $request->sendRequest())) {
            exit($ret->getMessage());
        }
        $content = $request->getResponseBody();
        if (preg_match('/SBM\.hub\.service\.getTable\(\[\'([^,\']*).*\'\]\)/', $content, $match)) {
            $service_id = $match[1];

            foreach ($allows[$service_id] as $model) {
                $model = $this->_normalizeModelName($model);
                $info  = & $this->_getProfileInfoByModel($model);


                $info->set('model',    $model);
                $info->set('decomail', 'allow', true);
            }
        }
    }

    function _parseProfileInfo_Kisekae($allows)
    {
        $request = new HTTP_Request('http://mb.softbank.jp/mb/service/3G/contents/change_arrange/');
        if (PEAR::isError($ret = $request->sendRequest())) {
            exit($ret->getMessage());
        }
        $content = $request->getResponseBody();
        if (preg_match('/SBM\.hub\.service\.getTable\(\[\'([^,\']*).*\'\]\)/', $content, $match)) {
            $service_id = $match[1];

            foreach ($allows[$service_id] as $model) {
                $model = $this->_normalizeModelName($model);
                $info  = & $this->_getProfileInfoByModel($model);


                $info->set('kisekae', 'allow', true);
            }
        }
    }

    function _normalizeDeviceName($device)
    {
        $device = trim($device);
        $device = ereg_replace("\r\n|\r|\n", ' ', $device);

        return $device;
    }

    function _normalizeModelName($model)
    {
        // 前後の空白を削除
        $model = trim($model);
        $model = mb_ereg_replace("\r\n|\r|\n", ' ', $model);

/*         // 数字とメーカー記号の間のスペースを削除 */
/*         $model = mb_ereg_replace('([^\d]*\d+)\s([A-Z].*)', '\1\2', $model); */

        // カッコを削除
        $model = mb_ereg_replace("\(.*\)", '', $model);

        $model = mb_ereg_replace(' [a-zA-Z ]{2,}*$', '', $model);

        // アラビア数字記号をIIIに置換
        $model = mb_ereg_replace("Ⅲ", 'III', $model);

        // アラビア数字記号をIIに置換
        $model = mb_ereg_replace("Ⅱ", 'II', $model);


        return $model;
    }

    /**
     * CSV形式の文字列を配列に分割する
     *
     * @accesspublic
     * @paramstring$csvCSV形式の文字列(1行分)
     * @paramstring$delimiterフィールドの区切り文字
     * @returnmixed(array):分割結果 Ethna_Error:エラー(行継続)
     */
    function explodeCSV($csv, $delimiter = ",")
    {
        $space_list = '';
        foreach (array(" ", "\t", "\r", "\n") as $c) {
            if ($c != $delimiter) {
                $space_list .= $c;
            }
        }

        $line_end = "";
        if (preg_match("/([$space_list]+)\$/sS", $csv, $match)) {
            $line_end = $match[1];
        }
        $csv = substr($csv, 0, strlen($csv)-strlen($line_end));
        $csv .= ' ';

        $field = '';
        $retval = array();

        $index = 0;
        $line_index = 0;
        $csv_len = strlen($csv);
        do {
            // 1. skip leading spaces
            if (preg_match("/^([$space_list]+)/sS", substr($csv, $index), $match)) {
                $index += strlen($match[1]);
            }
            if ($index >= $csv_len) {
                break;
            }

            // 2. read field
            if ($csv{$index} == '"') {
                // 2A. handle quote delimited field
                $index++;
                while ($index < $csv_len) {
                    if ($csv{$index} == '"') {
                        // handle double quote
                        if ($csv{$index+1} == '"') {
                            $field .= $csv{$index};
                            $index += 2;
                        } else {
                            // must be end of string
                            while ($csv{$index} != $delimiter && $csv{$index} != "\n" && $index < $csv_len) {
                                $index++;
                            }
                            if ($csv{$index} == $delimiter || $csv{$index} == "\n") {
                                $index++;
                            }
                            break;
                        }
                    } else {
                        // normal character
                        if (preg_match("/^([^\"]*)/S", substr($csv, $index), $match)) {
                            $field .= $match[1];
                            $index += strlen($match[1]);
                        }

                        if ($index == $csv_len) {
                            $field = substr($field, 0, strlen($field)-1);
                            $field .= $line_end;

                            // request one more line
                            return Ethna::raiseNotice(E_UTIL_CSV_CONTINUE);
                        }
                    }
                }
            } else {
                // 2B. handle non-quoted field
                if (preg_match("/^([^$delimiter\\n]*)/S", substr($csv, $index), $match)) {
                    $field .= $match[1];
                    $index += strlen($match[1]);
                }

                // remove trailing spaces
                $field = preg_replace("/[$space_list]+\$/S", '', $field);
                if ($csv{$index} == $delimiter) {
                    $index++;
                }
            }

            $retval[$line_index][] = $field;
            $field = '';

            // 行の終了
            if (preg_match("/^(\r\n|\r|\n)/", substr($csv, $index -1, 1), $match)) {
                $line_index++;
            }



        } while ($index < $csv_len);

        return $retval;
    }
}
