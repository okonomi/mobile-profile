<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Collector_Docomo_Display
{
    public function scrape()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/screen_area/index.html';

            $_Device = 'Mobile_Profile_Filter_Docomo_Device';

            $profile = new Diggin_Scraper();
            $profile->process('/td[last()-5]/span', "device => RAW, $_Device")
                    ->process('/td[last()-4]', "chars => TEXT")
                    ->process('/td[last()-2]', "browser => TEXT")
                    ->process('/td[last()-1]', "display => TEXT")
                    ->process('/td[last()-0]', "color => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->process('//table/tr[@class="acenter"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        foreach ($scraper->profile as $row) {
            $profile = array();

            $profile['device'] = $row['device']['device'];
            $profile['model']  = $row['device']['model'];

            preg_match_all('/(（([^）]*)）)?(\d+)×(\d+)/iu', $row['chars'], $match);
            $chars = array();
            for ($j = 0; $j < count($match[0]); $j++) {
                $key = !empty($match[2][$j]) ? $match[2][$j] : $j;
                $chars[$key] = array(
                    'char' => $match[3][$j],
                    'line' => $match[4][$j],
                );
            }
            $profile['character'] = $chars;

            preg_match_all('/(\d+)[^\d]+(\d+)(（([^\d）]*)）)?/iu', $row['browser'], $match);
            $screens = array();
            for ($j = 0; $j < count($match[0]); $j++) {
                $key = !empty($match[4][$j]) ? $match[4][$j] : $j;
                $screens[$key] = array(
                    'width'  => $match[1][$j],
                    'height' => $match[2][$j],
                );
            }
            $profile['browser'] = $screens;

            preg_match_all('/(（?([^\d）]*)）?)?(\d+)×(\d+)/iu', $row['display'], $match);
            $screens = array();
            for ($j = 0; $j < count($match[0]); $j++) {
                $key = !empty($match[2][$j]) ? $match[2][$j] : $j;
                $screens[$key] = array(
                    'width'  => $match[3][$j],
                    'height' => $match[4][$j],
                );
            }
            $profile['display'] = $screens;

            preg_match_all('/(白黒|カラー)(\d+)/iu', $row['color'], $match);
            $profile['color'] = array(
                'is_color' => $match[1][0] === 'カラー',
                'depth'    => $match[2][0],
            );

            $result[] = $profile;
        }

        return $result;
    }
}
