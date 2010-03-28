<?php
require_once 'Mobile/Profile/Docomo/Abstract.php';
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Docomo_Menuicon extends Mobile_Profile_Docomo_Abstract
{
    public function collect()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/menu_icon/index.html';

            $_Device = 'Mobile_Profile_Filter_Docomo_Device';
            $_Size   = 'Mobile_Profile_Filter_Size';

            $profile = new Diggin_Scraper();
            $profile->process('/td[not(@class) and @rowspan="2"][last()-2]/span', "device => RAW, $_Device")
                    ->process('/td[not(@class) and not(@rowspan)][last()-1]', "size => TEXT, $_Size")
                    ->process('/td[not(@class) and @rowspan="2"][last()-1]/span', "item => TEXT")
                    ->process('/td[not(@class) and @rowspan="2"][last()-0]/span', "embed => TEXT");
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

                $row['device']     = $profile['device']['device'];
                $row['model']      = $profile['device']['model'];
                $row['item']       = mb_split('／', $profile['item']);
                $row['icon_size']  = $profile['size'];
                $row['need_embed'] = ($profile['embed'] === '要');

                $device = $profile['device'];
            } else {
                $row['background_size'] = $profile['size'];
            }
        }
        $result[] = $row;

        return $result;
    }
}
