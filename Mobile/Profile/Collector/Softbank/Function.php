<?php
require_once 'Diggin/Scraper.php';
require_once dirname(dirname(dirname(__FILE__))).'/Adapter/Softbank/Attrstrip.php';


class Mobile_Profile_Collector_Softbank_Function
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=func';

            $_Model  = 'Mobile_Profile_Filter_Softbank_Model';
            $_String = 'Mobile_Profile_Filter_String';

            $profile = new Diggin_Scraper_Process();
            $profile->process('td[1]', "model => TEXT, $_Model")
                    ->process('td[2]', "lcd => TEXT")
                    ->process('td[3]', "memory => TEXT")
                    ->process('td[4]', "bluetooth => TEXT")
                    ->process('td[5]', "ir => RAW, $_String")
                    ->process('td[6]', "qr => TEXT")
                    ->process('td[7]', "tv => TEXT")
                    ->process('td[8]', "highspeed => TEXT")
                    ->process('td[9]', "camera => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->changeStrategy('Diggin_Scraper_Strategy_Flexible',
                                     new Mobile_Profile_Adapter_Softbank_Attrstrip())
                    ->process('//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        foreach ($scraper->profile as $profile) {
            $row = $profile;

            $arr = explode('*', $profile['lcd']);
            $row['lcd'] = array(
                'width'  => $arr[0],
                'height' => $arr[1],
            );

            $row['ir'] = mb_split('\n', $profile['ir']);

            $result[] = $row;
        }

        return $result;
    }
}
