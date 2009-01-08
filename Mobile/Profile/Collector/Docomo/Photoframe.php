<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Collector_Docomo_Photoframe
{
    public function scrape()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/frame_size/index.html';

            $_Device = 'Mobile_Profile_Filter_Docomo_Device';
            $_Size   = 'Mobile_Profile_Filter_Size';

            $profile = new Diggin_Scraper_Process();
            $profile->process('/td[last()-3]/span', "device => RAW, $_Device")
                    ->process('/td[position()>=last()-2]/span[count(./img)=0]', "size[] => TEXT, $_Size");
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
