<?php
require_once 'Mobile/Profile/Softbank/Abstract.php';
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Softbank_Useragent extends Mobile_Profile_Softbank_Abstract
{
    public function collect()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=ua';

            $_Model  = 'Mobile_Profile_Filter_Softbank_Model';
            $_String = 'Mobile_Profile_Filter_String';

            $profile = new Diggin_Scraper();
            $profile->process('td[last()-1]', "model => TEXT, $_Model")
                    ->process('td[last()-0]', "ua => RAW, $_String");
            $scraper = new Diggin_Scraper();
            $scraper->process('//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $ua_names = array(
            'browser',
            'appli',
            'widget',
            'flash',
            'fullbrowser',
        );
        $result = array();
        $model = null;
        foreach ($scraper->profile as $profile) {
            if (isset($profile['model'])) {
                if (!is_null($model)) {
                    $result[] = $row;
                }
                $model = $profile['model'];

                $row = array();
                $row['model']     = $model;
                $row['useragent'] = array();
            }

            $ua_name = $ua_names[count($row['useragent'])];
            $ua = preg_replace('/\n/', ' ', $profile['ua']);
            if (preg_match('/^×$/', $ua)) {
                $ua = null;
            }
            $row['useragent'][$ua_name] = $ua;
        }
        $result[] = $row;

        return $result;
    }
}
