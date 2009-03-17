<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Au_Brew
{
    public function scrape()
    {
        try {
            $url = 'http://www.au.kddi.com/ezfactory/service/brew.html';

            $scraper = new Diggin_Scraper();
            $scraper->process('//tr[@bgcolor="#CCCCCC"]//tr[@bgcolor="#FFFFFF"]/td[not(@align)]', "model[] => DECODE")
                    ->process('//tr[@bgcolor="#CCCCCC"]//tr[@bgcolor="#FFFFFF"]/td[@align="center"]', "version[] => DECODE")
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        for ($i = 0; $i < count($scraper->model); $i++) {
            if (!is_numeric($scraper->version[$i])) {
                continue;
            }

            $row = array(
                'model'   => $scraper->model[$i],
                'version' => $scraper->version[$i],
            );

            $result[] = $row;
        }

        return $result;
    }
}
