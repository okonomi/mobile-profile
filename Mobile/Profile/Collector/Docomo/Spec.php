<?php
require_once 'HTTP/Request.php';


class Mobile_Profile_Collector_Docomo_Spec
{
    function scrape()
    {
        $url = 'http://www.nttdocomo.co.jp/binary/pdf/service/imode/make/content/spec/imode_spec.pdf';

        $options = array (
            'timeout'        => '10', // タイムアウトの秒数指定
            'allowRedirects' => true, // リダイレクトの許可設定(true/false)
            'maxRedirects'   => 3,    // リダイレクトの最大回数
        );
        $request = new HTTP_Request();
        $request->reset($url, $options);
        $response = $request->sendRequest();
        if (PEAR::isError($response)) {
            echo $response->getMessage();
            return false;
        }
        $content = $request->getResponseBody();
        $imode_spec = self::_parseImodeSpec($content);

        $result = array();
        foreach ($imode_spec as $spec) {
            $decomail_spec = array(
                // [0] = デコメ送信
                // [1] = デコメ編集
                // [2] = デコメテンプレート対応
                // [3] = デコメテンプレートDL
                // [4] = デコメテンプレートタイトル対応
                -1 => array(0, 0, 0, 0, 0),
                 0 => array(1, 1, 1, 1, 0),
                 1 => array(1, 1, 0, 0, 0),
                 2 => array(0, 0, 0, 0, 0),
                 3 => array(1, 0, 1, 0, 0),
                 4 => array(1, 1, 1, 1, 1),
            );

            if (preg_match('/([\d\.]+)(\*(\d+))?$/', $spec['decomail'], $match)) {
                $decomail_version = $match[1];

                if (isset($match[3])) {
                    $decomail_specialtype = 0;
                } else {
                    $decomail_specialtype = (int)$match[3];
                }
            } else {
                $decomail_version     = null;
                $decomail_specialtype = -1;
            }

            unset($spec['decomail']);
            $spec['decomail_version']        = $decomail_version;
            $spec['decomail_send']           = (boolean)$decomail_spec[$decomail_specialtype][0];
            $spec['decomail_edit']           = (boolean)$decomail_spec[$decomail_specialtype][1];
            $spec['decomail_template']       = (boolean)$decomail_spec[$decomail_specialtype][2];
            $spec['decomail_template_dl']    = (boolean)$decomail_spec[$decomail_specialtype][3];
            $spec['decomail_template_title'] = (boolean)$decomail_spec[$decomail_specialtype][4];

            $result[] = $spec;
        }

        return $result;
    }

    function _parseImodeSpec($content)
    {
        $tmpfile = tempnam(sys_get_temp_dir(), '');
        $outfile = "{$tmpfile}.txt";

        file_put_contents($tmpfile, $content);

        exec("pdftotext -layout -nopgbrk -eol unix {$tmpfile} {$outfile}");

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
            'decomeanime',
            'toruca',
            'ichannel',
            'emoji',
            'gps',
            'frame',
            'menuicon',
            'kisekae',
            'machichara',
            'iconcier',
            'barcode',
            'ssl',
            'drm',
            'etc',
        );

        $fp = fopen($outfile, 'r');
        $imode_spec = array();


        $lines = array();
        while($line = fgets($fp, 1024)) {
            if (preg_match('/\*\d+$/', $line)) {
                $lines[count($lines) -1] .= ' '. $line;
            } else {
                $lines[] = $line;
            }
        }
        fclose($fp);

        unlink($outfile);
        unlink($tmpfile);


        foreach ($lines as $data) {
            $data = mb_split('[\s　]+', $data);
            if (reset($data) === '') {
                array_shift($data);
            }
            if (end($data) === '') {
                array_pop($data);
            }
            if ($data[0] === 'FOMA') {
                array_shift($data);
                array_shift($data);
            }

            if (preg_match('/^[A-Z]{1,2}\-?\d+/', $data[0])) {
                $device = array_shift($data);

                if (preg_match('/^\(/', $data[0])) {
                    $model = '';
                    do {
                        $model .= ' '.array_shift($data);
                    } while (!preg_match('/\)$/', $model));
                    $model = substr($model, 2, strlen($model) -3);
                } else {
                    $model = $device;
                }

                if (count($data) >= count($contents_type)) {
                    list ($data, $etc) = array_chunk($data, count($contents_type) -1);
                    $data[] = implode(' ', $etc);
                }

                $tmp  = array_pad($data, count($contents_type), '');
                $spec = array_combine($contents_type, $tmp);


                $device = str_replace('μ', 'myu', $device);
                $device = str_replace('-', '', $device);

                $imode_spec[] = array(
                    'device'   => $device,
                    'model'    => $model,
                ) + $spec;
            }
        }

        return $imode_spec;
    }
}
