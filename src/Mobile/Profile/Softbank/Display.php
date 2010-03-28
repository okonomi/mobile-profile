<?php
require_once 'Mobile/Profile/Softbank/Abstract.php';
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Softbank_Display extends Mobile_Profile_Softbank_Abstract
{
    public function collect()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=display';

            $_Model  = 'Mobile_Profile_Filter_Softbank_Model';
            $_Screen = 'Mobile_Profile_Filter_Softbank_Screen';

            $profile = new Diggin_Scraper();
            $profile->process('td[1]', "model => TEXT, $_Model")
                    ->process('td[2]', "browser_screen => TEXT, $_Screen")
                    ->process('td[3]', "browser_chars => TEXT")
                    ->process('td[4]', "appli_screen => TEXT")
                    ->process('td[5]', "appli_font => TEXT")
                    ->process('td[6]', "widget_solo => TEXT, $_Screen")
                    ->process('td[7]', "widget_wallp => TEXT, $_Screen")
                    ->process('td[8]', "flash => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->process('//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        foreach ($scraper->profile as $profile) {
            $row = array();

            $row['model']          = $profile['model'];
            $row['browser_screen'] = $profile['browser_screen'];
            $row['widget_solo']    = $profile['widget_solo'];
            $row['widget_wallp']   = $profile['widget_wallp'];

            preg_match_all('/(【([^】]+)】)?(([^:]+):)?(\d+) x (\d+)(\((デフォルト|ﾃﾞﾌｫﾙﾄ)\))?/',
                           $profile['browser_chars'], $match);
            $chars = array();
            $key0 = 0;
            for ($i = 0; $i < count($match[0]); $i++) {
                $key0 = !empty($match[2][$i]) ? $match[2][$i] : $key0;

                $key = !empty($match[4][$i]) ? $match[4][$i] : $i;
                $chars[$key0][$key] = array(
                    'char' => $match[5][$i],
                    'line' => $match[6][$i],
                );
            }
            $row['browser_chars'] = $chars;

            $screens = array();
            if (preg_match_all('/([^\d]+)?(\d+) x (\d+)/', $profile['appli_screen'], $match)) {
                for ($i = 0; $i < count($match[0]); $i++) {
                    $key = !empty($match[1][$i]) ? $match[1][$i] : $i;
                    $screens[$key] = array(
                        'width'  => $match[2][$i],
                        'height' => $match[3][$i],
                    );
                }
            } else {
                $screens[] = array();
            }
            $row['appli_screen'] = $screens;

            $fonts = array();
            if (preg_match_all('/([^\d]+)?(\d+) x (\d+)/', $profile['appli_font'], $match)) {
                for ($i = 0; $i < count($match[0]); $i++) {
                    $key = !empty($match[1][$i]) ? $match[1][$i] : $i;
                    $fonts[$key] = array(
                        'width'  => $match[2][$i],
                        'height' => $match[3][$i],
                    );
                }
            } else {
                $fonts[] = array();
            }
            $row['appli_font'] = $fonts;

            $screen = array();
            if (preg_match('/(\d+) x (\d+)/', $profile['flash'], $match)) {
                $screen = array(
                    'width'  => $match[1],
                    'height' => $match[2],
                );
            }
            $row['flash'] = $screen;

            $result[] = $row;
        }

        return $result;
    }
}
