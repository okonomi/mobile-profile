<?php
require_once 'Diggin/Scraper.php';
require_once dirname(dirname(dirname(__FILE__))).'/Filter/Device.php';
require_once dirname(dirname(dirname(__FILE__))).'/Filter/Size.php';


class Mobile_Profile_Collector_Docomo_Menuicon
{
    public function scrape()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/menu_icon/index.html';

            $profile = new Diggin_Scraper_Process();
            $profile->process('/td[not(@class) and @rowspan="2"][last()-2]/span', 'device => "RAW", DocomoDevice')
                    ->process('/td[not(@class) and not(@rowspan)][last()-1]', 'size => "TEXT", Size')
                    ->process('/td[not(@class) and @rowspan="2"][last()-1]/span', 'item => "TEXT"')
                    ->process('/td[not(@class) and @rowspan="2"][last()-0]/span', 'embed => "TEXT"');
            $scraper = new Diggin_Scraper();
            $scraper->process('//table/tr', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        $device = null;
        foreach ($scraper->profile as $profile) {
            if (isset($profile['device'])) {
                if (!is_null($device)) {
                    $result[] = $row;
                }

                $row = array();
                $row['device']    = $profile['device']['device'];
                $row['model']     = $profile['device']['model'];
                $row['item']      = mb_split('／', $profile['item']);
                $row['icon_size'] = $profile['size'];

                $device = $profile['device'];
            } else {
                $row['bg_size'] = $profile['size'];
            }
        }

        return $result;
    }
}