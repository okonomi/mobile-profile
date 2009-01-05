<?php
require_once 'Diggin/Scraper.php';
require_once 'Zend/Http/Client.php';


class Mobile_Profile_Collector_Softbank_Series
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=http';

            $_Model = 'Mobile_Profile_Filter_Softbank_Model';

            $client = new Zend_Http_Client();
            $client->setAdapter('Mobile_Profile_Adapter_Softbank_Attrstrip');

            $profile = new Diggin_Scraper_Process();
            $profile->process('.', "color => @bgcolor")
                    ->process('td[1]', "cell => TEXT, $_Model");
            $scraper = new Diggin_Scraper();
            $scraper->setHttpClient($client);
            $scraper->process('//tr[@bgcolor="#CCCCCC" or @bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        $series = null;
        foreach ($scraper->profile as $profile) {
            $row = array();

            if ($profile['color'] === '#CCCCCC') {
                $series = $profile['cell'];
            } else {
                $row['model']  = $profile['cell'];
                $row['series'] = $series;

                $result[] = $row;
            }
        }

        return $result;
    }
}
