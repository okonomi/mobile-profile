<?php
require_once 'Diggin/Scraper.php';
require_once 'Zend/Http/Client.php';
require_once dirname(dirname(dirname(__FILE__))).'/Adapter/Softbank/Attrstrip.php';


class Mobile_Profile_Collector_Softbank_Httpheader
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=http';

            $client = new Zend_Http_Client();
            $client->setAdapter(new Mobile_Profile_Adapter_Softbank_Attrstrip());

            $profile = new Diggin_Scraper_Process();
            $profile->process('td[1]', 'model => "TEXT"')
                    ->process('td[2]', 'device => "TEXT"')
                    ->process('td[3]', 'display => "TEXT"')
                    ->process('td[4]', 'color => "TEXT"')
                    ->process('td[5]', 'sound => "TEXT"')
                    ->process('td[6]', 'smaf => "TEXT"')
                    ->process('td[7]', 'display-info => "TEXT"')
                    ->process('td[8]', 'unique-id => "TEXT"');

            $scraper = new Diggin_Scraper();
            $scraper->setHttpClient($client);
            $scraper->process('//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        foreach ($scraper->profile as $profile) {
            $row = array();

            $row['device'] = $profile['device'];
            $row['model']  = $profile['model'];

            preg_match('/(G|C)(\d+)/', $profile['color'], $match);
            $row['color'] = array(
                'is_color' => $match[1] === 'C',
                'depth'    => $match[2],
            );

            $result[] = $row;
        }

        return $result;
    }
}