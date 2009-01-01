<?php
require_once 'Diggin/Scraper.php';
require_once 'Zend/Http/Client.php';
require_once dirname(dirname(dirname(__FILE__))).'/Adapter/Softbank/Attrstrip.php';


class Mobile_Profile_Collector_Softbank_Useragent
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=ua';

            $client = new Zend_Http_Client();
            $client->setAdapter(new Mobile_Profile_Adapter_Softbank_Attrstrip());

            $profile = new Diggin_Scraper_Process();
            $profile->process('td[last()-1]', 'model => "TEXT"')
                    ->process('td[last()-0]', 'ua => "TEXT"');

            $scraper = new Diggin_Scraper();
            $scraper->setHttpClient($client);
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
            'fullbworser',
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
            $ua = $profile['ua'];
            if (preg_match('/^(-|ー|−)$/', $ua)) {
                $ua = null;
            }
            $row['useragent'][$ua_name] = $ua;
        }
        $result[] = $row;

        return $result;
    }
}