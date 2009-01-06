<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Collector_Au_Basic
{
    public function scrape()
    {
        try {
            $url = 'http://www.au.kddi.com/ezfactory/tec/spec/new_win/ezkishu.html';

            $format = new Diggin_Scraper_Process();
            $format->process('/td[7]', "gif => TEXT")
                   ->process('/td[8]', "jpeg => TEXT")
                   ->process('/td[9]', "png => TEXT")
                   ->process('/td[10]', "bmp4 => TEXT")
                   ->process('/td[11]', "bmp2 => TEXT");
            $profile = new Diggin_Scraper_Process();
            $profile->process('/td[1]', "model => TEXT")
                    ->process('/td[2]', "browser => TEXT")
                    ->process('/td[3]', "color => TEXT")
                    ->process('/td[4]', "chars => TEXT")
                    ->process('/td[5]', "browser_size => TEXT")
                    ->process('/td[6]', "wallpaper => TEXT")
                    ->process('.', array('format[]' => $format))
                    ->process('/td[12]', "flash => TEXT")
                    ->process('/td[13]', "attach => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->process('//table[@width="892"]//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = $scraper->profile;

        return $result;
    }
}
