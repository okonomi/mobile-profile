<?php
require_once 'Diggin/Scraper.php';
require_once 'Filter/Device.php';


class Mobile_Profile_Collector_Docomo_Display
{
    public function scrape()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/screen_area/index.html';

            $profile = new Diggin_Scraper_Process();
            $profile->process('/td[last()-5]/span', 'device => "RAW", DocomoDevice')
                    ->process('/td[last()-4]', 'font => "TEXT"')
                    ->process('/td[last()-3]/span', 'character => "RAW"')
                    ->process('/td[last()-2]', 'browser => "TEXT"')
                    ->process('/td[last()-1]', 'display => "TEXT"')
                    ->process('/td[last()-0]', 'color => "TEXT"');
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

            preg_match_all('/(（([^）]*)）)?(\d+)×(\d+)/iu', $row['font'], $match);
            $fonts = array();
            for ($j = 0; $j < count($match[0]); $j++) {
                $key = !empty($match[2][$j]) ? $match[2][$j] : $j;
                $fonts[$key] = array(
                    'width'  => $match[3][$j],
                    'height' => $match[4][$j],
                );
            }
            $profile['font'] = $fonts;

            preg_match_all('/(（([^\n]*)）\n?)?(\d+)/iu', (string)$row['character'], $match);
            $characters = array();
            for ($j = 0; $j < count($match[0]); $j++) {
                $key = !empty($match[2][$j]) ? $match[2][$j] : $j;
                $characters[$key] = $match[3][$j];
            }
            $profile['character'] = $characters;

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
