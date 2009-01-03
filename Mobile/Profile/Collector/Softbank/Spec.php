<?php
require_once 'HTTP/Request.php';


class Mobile_Profile_Collector_Softbank_Spec
{
    public function scrape($username, $password)
    {
        $csv   = self::_getDeviceSpecList($username, $password);
        $csv   = mb_convert_encoding($csv, 'UTF-8', 'sjis-win');
        $specs = self::_explodeCSV($csv);

        return $specs;
    }

    protected function _getDeviceSpecList($username, $password)
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
        $request->addPostData('email',     $username);
        $request->addPostData('pass_text', $password);
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

    protected function _explodeCSV($csv)
    {
        /*
          SoftBankのスペックCSVはちょっと特殊で
           - 改行コードはCRLFで統一
           - 値は必ずダブルクォートでくくられている
          ので、それを前提とした処理になっています
         */

        $index      = 0;
        $line_index = 0;
        $csv_len    = strlen($csv);

        $result = array();

        do {
            if ($csv{$index} === '"') {
                $inner_start = $index +1;
                do {
                    $index++;

                    if ($index >= $csv_len || $csv{$index} === '"') {
                        $result[$line_index][] = substr($csv, $inner_start, $index - $inner_start);
                        $index++;
                        break;
                    }
                } while(true);
            } else if (preg_match("/^(\r\n)/", substr($csv, $index, 2), $match)) {
                $line_index++;
                $index += strlen($match[1]);
            } else {
                $index++;
            }

            if ($index >= $csv_len) {
                break;
            }
        } while(true);


        return $result;
    }
}
