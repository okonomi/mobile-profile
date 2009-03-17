<?php
require_once 'HTTP/Request2.php';


class Mobile_Profile_Softbank_Spec
{
    protected $options = array();


    public function scrape()
    {
        $csv   = self::_getDeviceSpecList();
        $csv   = mb_convert_encoding($csv, 'UTF-8', 'sjis-win');
        $specs = self::_explodeCSV($csv);

        return $specs;
    }

    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    protected function _getDeviceSpecList()
    {
        try {
            foreach (array('username', 'password') as $option) {
                if (!isset($this->options[$option])) {
                    throw new Exception('option is empty: '.$option);
                }
            }

            $request = new HTTP_Request2();
            $request->setConfig(array('ssl_verify_peer' => false));


            // ログインページをGET
            $response =
                    $request->setUrl('https://creation.mb.softbank.jp/members/mem_login.html')
                            ->setMethod(HTTP_Request2::METHOD_GET)
                            ->send();

            // セッションIDらしきものが発行される
            $cookie = $response->getCookies();
            $id = $cookie[0];

            // ログイン
            $response =
                    $request->setUrl('https://creation.mb.softbank.jp/members/mem_login.html')
                            ->setMethod(HTTP_Request2::METHOD_POST)
                            ->addPostParameter(array(
                                           'email'     => $this->options['username'],
                                           'pass_text' => $this->options['password'],
                                           'save'      =>'1',
                                       ))
                            ->send();
            $cookie = $response->getCookies();

            // スペック一覧ダウンロード
            $request->setUrl('http://creation.mb.softbank.jp/terminal/spec_download.html')
                    ->setMethod(HTTP_Request2::METHOD_POST)
                    ->addPostParameter('lup', 'y')
                    ->addCookie($id['name'], $id['value']);
            foreach ($cookie as $val) {
                $request->addCookie($val['name'], $val['value']);
            }
            $response = $request->send();

            // スペック表ゲット
            $csv = $response->getBody();

            // ログアウト
            $request->setUrl('http://creation.mb.softbank.jp/members/mem_logout.html')
                    ->setMethod(HTTP_Request2::METHOD_GET)
                    ->addCookie($id['name'], $id['value'])
                    ->send();
        } catch (HTTP_Request2_Exception $e) {
            throw $e;
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
