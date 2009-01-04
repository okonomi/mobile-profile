<?php
require_once 'Diggin/Scraper.php';
require_once 'Zend/Http/Client.php';


class Mobile_Profile_Collector_Softbank_Appli
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=sappli';

            $_Model = 'Mobile_Profile_Filter_Softbank_Model';

            $client = new Zend_Http_Client();
            $client->setAdapter('Mobile_Profile_Adapter_Softbank_Attrstrip');

            $profile = new Diggin_Scraper_Process();
            $profile->process('td[1]', "model => TEXT, $_Model")
                    ->process('td[2]', "heap => TEXT")
                    ->process('td[3]', "cldc => TEXT")
                    ->process('td[4]', "midp => TEXT")
                    ->process('td[5]', "extension => TEXT")
                    ->process('td[6]', "felica => TEXT")
                    ->process('td[7]', "location => TEXT")
                    ->process('td[8]', "size => TEXT")
                    ->process('td[9]', "total => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->setHttpClient($client);
            $scraper->process('//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        foreach ($scraper->profile as $profile) {
            $row = $profile;

            $arr = explode('/', $profile['size']);
            $row['size'] = array(
                'jad'          => $arr[0],
                'jar'          => $arr[1],
                'recordsctore' => $arr[2],
                'total'        => $profile['total'],
            );
            unset($row['total']);

            $result[] = $row;
        }

        return $result;
    }
}
