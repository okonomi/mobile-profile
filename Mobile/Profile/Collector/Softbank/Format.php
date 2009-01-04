<?php
require_once 'Diggin/Scraper.php';
require_once 'Zend/Http/Client.php';


class Mobile_Profile_Collector_Softbank_Format
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=form';

            $_Model = 'Mobile_Profile_Filter_Softbank_Model';

            $client = new Zend_Http_Client();
            $client->setAdapter('Mobile_Profile_Adapter_Softbank_Attrstrip');

            $profile = new Diggin_Scraper_Process();
            $profile->process('td[1]', "model => TEXT, $_Model")
                    ->process('td[2]', "jpeg => TEXT")
                    ->process('td[3]', "png => TEXT")
                    ->process('td[4]', "gif => TEXT")
                    ->process('td[5]', "smaf => TEXT")
                    ->process('td[6]', "midi => TEXT")
                    ->process('td[7]', "mp4 => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->setHttpClient($client);
            $scraper->process('//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = $scraper->profile;

        return $result;
    }
}
