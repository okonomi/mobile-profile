<?php
require_once 'Diggin/Scraper.php';
require_once 'Mobile/Profile/Adapter/Softbank/Attrstrip.php';


class Mobile_Profile_Collector_Softbank_Useragent
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=ua';

            $_Model  = 'Mobile_Profile_Filter_Softbank_Model';
            $_String = 'Mobile_Profile_Filter_String';

            $profile = new Diggin_Scraper_Process();
            $profile->process('td[last()-1]', "model => TEXT, $_Model")
                    ->process('td[last()-0]', "ua => RAW, $_String");
            $scraper = new Diggin_Scraper();
            $scraper->changeStrategy('Diggin_Scraper_Strategy_Flexible',
                                     new Mobile_Profile_Adapter_Softbank_Attrstrip())
                    ->process('//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
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
            if (preg_match('/^(-|ー|−)$/', $ua)) {
                $ua = null;
            }
            $row['useragent'][$ua_name] = $ua;
        }
        $result[] = $row;

        return $result;
    }
}
