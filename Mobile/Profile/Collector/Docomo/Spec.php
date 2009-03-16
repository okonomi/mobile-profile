<?php
require_once 'HTTP/Request2.php';


class Mobile_Profile_Collector_Docomo_Spec
{
    public function scrape()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/binary/pdf/service/imode/make/content/spec/imode_spec.pdf';

            $request = new HTTP_Request2($url, HTTP_Request2::METHOD_GET);

            $response = $request->send();
            if ($response->getStatus() !== 200) {
                throw new Exception('Server returned status: '.$response->getStatus());
            }

            $content = $response->getBody();
        } catch (HTTP_Request2_Exception $e) {
            throw $e;
        }

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

                if (count($match) < 4) {
                    $decomail_specialtype = 0;
                } else {
                    $decomail_specialtype = (int)$match[3];
                }
            } else {
                $decomail_version     = null;
                $decomail_specialtype = -1;
            }

            $spec['decomail'] = array(
                'version'        => $decomail_version,
                'send'           => (boolean)$decomail_spec[$decomail_specialtype][0],
                'edit'           => (boolean)$decomail_spec[$decomail_specialtype][1],
                'template'       => (boolean)$decomail_spec[$decomail_specialtype][2],
                'template_dl'    => (boolean)$decomail_spec[$decomail_specialtype][3],
                'template_title' => (boolean)$decomail_spec[$decomail_specialtype][4],
            );

            $result[] = $spec;
        }

        return $result;
    }

    private function _parseImodeSpec($content)
    {
        $_encoding = mb_regex_encoding('UTF-8');


        $tmpfile = tempnam(sys_get_temp_dir(), '');
        $outfile = "{$tmpfile}.txt";

        file_put_contents($tmpfile, $content);

        exec("pdftotext -layout -nopgbrk -enc UTF-8 -eol unix {$tmpfile} {$outfile}");

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
        $continued = false;
        while($line = fgets($fp, 10240)) {
            if (strlen($line) <= 2) {
                continue;
            }

            if ($continued) {
                $_1st = $lines[count($lines) -1];
                $_2nd = $line;

                $lines[count($lines) -1] = array($_1st, $_2nd);
            } else {
                $lines[] = $line;
            }

            // 「*1」とかで終わってる
            if (preg_match('/\*\d+$/iu', $line)) {
                $continued = true;
            } else {
                $continued = false;
            }
        }
        fclose($fp);

        unlink($outfile);
        unlink($tmpfile);


        foreach ($lines as $data) {
            $come = null;
            if (is_array($data)) {
                $tmp  = mb_split('[\s]+', $data[0]);
                $come = $tmp[count($tmp) -2];
                $data = reset($tmp).' '.$data[1];
            }

            $data = mb_split('[\s　]+', $data);

            if (reset($data) === '') {
                array_shift($data);
            }
            if (end($data) === '') {
                array_pop($data);
            }
            if (count($data) == 0) {
                continue;
            }

            if ($data[0] === 'FOMA') {
                array_shift($data);
                array_shift($data);
            } elseif ( preg_match('/^\d{3}i?$/', $data[0])) {
                array_shift($data);
            }

            if (preg_match('/^[A-Z]{1,2}\-?\d+/iu', $data[0])) {
                $device = array_shift($data);

                if (preg_match('/^\(/iu', $data[0])) {
                    $model = '';
                    do {
                        $model .= ' '.array_shift($data);
                    } while (!preg_match('/\)$/iu', $model));
                    $model = substr($model, 2, strlen($model) -3);
                } else {
                    $model = $device;
                }

                $tmp  = array_pad($data, count($contents_type), '');
                $spec = array_combine($contents_type, $tmp);

                if (!is_null($come)) {
                    $spec['decomail'] .= $come;
                }


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
