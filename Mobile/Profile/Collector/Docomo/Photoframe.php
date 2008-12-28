<?php
require_once 'Diggin/Scraper.php';
require_once 'Filter/Device.php';
require_once 'Filter/Size.php';


class Mobile_Profile_Collector_Docomo_Photoframe
{
    function scrape()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/frame_size/index.html';

            $profile = new Diggin_Scraper_Process();
            $profile->process('/td[last()-3]/span', 'device => "RAW", DocomoDevice')
                    ->process('/td[position()>=last()-2]/span[count(./img)=0]', 'size[] => "TEXT", Size');
            $scraper = new Diggin_Scraper();
            $scraper->process('//table/tr[@class="acenter"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        $device = null;
        $size   = array();
        foreach ($scraper->profile as $profile) {
            if (isset($profile['device'])) {
                if (!is_null($device)) {
                    $row['size'] = $size;
                    $result[] = $row;
                }

                $row = array();
                $row['device'] = $profile['device']['device'];
                $row['model']  = $profile['device']['model'];

                $device = $profile['device'];
                $size   = array();
            }

            $size = array_merge($size, $profile['size']);
        }
        $row['size'] = $size;
        $result[] = $row;

        return $result;
    }
}
