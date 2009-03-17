<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Au_Deviceid
{
    public function scrape()
    {
        try {
            $url = 'http://www.au.kddi.com/ezfactory/tec/spec/4_4.html';

            $scraper = new Diggin_Scraper();
            $scraper->process('//tr[@bgcolor="#CCCCCC"]//tr[not(@align)]/td[@bgcolor="#F2F2F2"]', "model[] => TEXT")
                    ->process('//tr[@bgcolor="#CCCCCC"]//tr[not(@align)]/td[not(@bgcolor)]', "deviceid[] => TEXT")
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        for ($i = 0; $i < count($scraper->model); $i++) {
            $row = array(
                'model'    => $scraper->model[$i],
                'deviceid' => $scraper->deviceid[$i],
            );

            if ($row['deviceid'] === 'CA23') {
                $row['model'] = 'A5401CA/CA II';
            } elseif ($row['deviceid'] === 'TS25') {
                if (!preg_match('/カメラ/', $row['model'])) {
                    $row['model'] = 'A1304T/T II';
                }
            }

            $result[$row['model']] = $row;
        }

        return array_values($result);
    }
}
