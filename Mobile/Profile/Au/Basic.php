<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Au_Basic
{
    public function scrape()
    {
        try {
            $url = 'http://www.au.kddi.com/ezfactory/tec/spec/new_win/ezkishu.html';

            $_Size = 'Mobile_Profile_Filter_Size';

            $format = new Diggin_Scraper();
            $format->process('/td[7]', "gif => TEXT")
                   ->process('/td[8]', "jpeg => TEXT")
                   ->process('/td[9]', "png => TEXT")
                   ->process('/td[10]', "bmp4 => TEXT")
                   ->process('/td[11]', "bmp2 => TEXT");
            $profile = new Diggin_Scraper();
            $profile->process('/td[1]', "model => TEXT")
                    ->process('/td[2]', "browser_type => TEXT")
                    ->process('/td[3]', "color => TEXT")
                    ->process('/td[4]', "chars => TEXT")
                    ->process('/td[5]', "browser => TEXT, $_Size")
                    ->process('/td[6]', "display => TEXT")
                    ->process('.', array('format[]' => $format))
                    ->process('/td[12]', "flash => TEXT")
                    ->process('/td[13]', "attach => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->process('//table[@width="892"]//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $flash_version = array(
            "●" => '2.0',
            "◎" => '1.1',
            "○" => '1.1',
            "−" => null,
        );

        $result = array();
        foreach ($scraper->profile as $profile) {
            $row = array();

            $row['model']        = $profile['model'];
            $row['browser_type'] = $profile['browser_type'];

            preg_match('/(モノクロ|カラー)\((.*)色\)/', $profile['color'], $match);
            $row['display_color'] = array(
                'is_color' => $match[1] === 'カラー',
                'depth'    => self::_kanjiToInt($match[2]),
            );

            preg_match('/(\d+)×(\d+)/', $profile['chars'], $match);
            $row['display_chars'] = array(
                'char' => $match[1],
                'line' => $match[2],
            );

            $row['browser_screen'] = $profile['browser'];

            preg_match_all('/(\d+)×(\d+)( ([^\d\(\)\s]+))?/', $profile['display'], $match);
            for ($i = 0; $i < count($match[0]); $i++) {
                $key = !empty($match[4][$i]) ? $match[4][$i] : $i;
                $row['display_screen'][$key] = array(
                    'width'  => $match[1][$i],
                    'height' => $match[2][$i],
                );
            }

            $row['format']        = $profile['format'];
            $row['flash_version'] = $flash_version[$profile['flash']];
            $row['attach']        = $profile['attach'];


            $result[] = $row;
        }

        return $result;
    }

    protected function _kanjiToInt($value)
    {
        $value = str_replace(',', '', $value);

        if (preg_match('/(\d+)万$/', $value, $match)) {
            $value = $match[1] * 10000;
        } elseif ($value === '6万5千') {
            $value = 65000;
        } else {
            $value = (int)$value;
        }

        return (string)$value;
    }
}
