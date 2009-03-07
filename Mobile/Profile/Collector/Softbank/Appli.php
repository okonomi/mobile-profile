<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Collector_Softbank_Appli
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=sappli';

            $_Model = 'Mobile_Profile_Filter_Softbank_Model';

            $profile = new Diggin_Scraper();
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
                'jad'         => $arr[0],
                'jar'         => $arr[1],
                'recordstore' => $arr[2],
                'total'       => $profile['total'],
            );
            unset($row['total']);

            $result[] = $row;
        }

        return $result;
    }
}
