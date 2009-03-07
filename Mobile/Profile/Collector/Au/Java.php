<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Collector_Au_Java
{
    public function scrape()
    {
        try {
            $url = 'http://www.au.kddi.com/ezfactory/tec/spec/ezplus.html';

            $profile = new Diggin_Scraper();
            $profile->process('//td[1]/div', "version => TEXT")
                    ->process('//td[2]/div', "models => TEXT");
            $scraper = new Diggin_Scraper();
            $scraper->process('//tr[13]/td/table//tr[@bgcolor="#FFFFFF"]', array('profile[]' => $profile))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        foreach ($scraper->profile as $profile) {
            preg_match('/^au(.*)$/', $profile['models'], $match);
            $models = mb_split('\s*/\s*', $match[1]);

            foreach ($models as $model) {
                $model = preg_replace('/ \(.*\)/', '', $model);

                $row = array(
                    'model'   => $model,
                    'version' => $profile['version'],
                );

                $result[] = $row;
            }

        }

        return $result;
    }
}
