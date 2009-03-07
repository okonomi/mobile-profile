<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Collector_Softbank_Service
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=service';

            $_Model = 'Mobile_Profile_Filter_Softbank_Model';

            $profile = new Diggin_Scraper();
            $profile->process('td[1]', "model => TEXT, $_Model")
                    ->process('td[2]', "appli => TEXT")
                    ->process('td[3]', "widget => TEXT")
                    ->process('td[4]', "flash => TEXT")
                    ->process('td[5]', "gps => TEXT")
                    ->process('td[6]', "agps => TEXT")
                    ->process('td[7]', "felica => TEXT")
                    ->process('td[8]', "fullbrowser => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->process('//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        foreach ($scraper->profile as $profile) {
            $row = $profile;

            $row['flash'] = str_replace('&trade;', ' ', $row['flash']);

            $result[] = $row;
        }

        return $result;
    }
}
