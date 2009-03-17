<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Softbank_Series
{
    public function scrape()
    {
        try {
            $url = 'http://creation.mb.softbank.jp/terminal/?lup=y&cat=http';

            $_Model = 'Mobile_Profile_Filter_Softbank_Model';

            $profile = new Diggin_Scraper();
            $profile->process('.', "color => @bgcolor")
                    ->process('td[1]', "cell => TEXT, $_Model");
            $scraper = new Diggin_Scraper();
            $scraper->process('//tr[@bgcolor="#CCCCCC" or @bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        $series = null;
        $generation = null;
        foreach ($scraper->profile as $profile) {
            $row = array();

            if ($profile['color'] === '#CCCCCC') {
                $series = $profile['cell'];

                if (preg_match('/アプリ非対応|50k/iu', $series)) {
                    $generation = '2G';
                } elseif (preg_match('/(100|256)k/iu', $series)) {
                    $generation = '2.5G';
                } else {
                    $generation = '3GC';
                }
            } else {
                $row['model']      = $profile['cell'];
                $row['series']     = $series;
                $row['generation'] = $generation;

                $result[] = $row;
            }
        }

        return $result;
    }
}
