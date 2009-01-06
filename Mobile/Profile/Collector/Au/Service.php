<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Collector_Au_Service
{
    public function scrape()
    {
        $serviceid_list = array();


        // サービスカテゴリのURLを取得
        try {
            $url = 'http://www.au.kddi.com/service/index.html';

            $scraper = new Diggin_Scraper();
            $scraper->process('p.contentsBoxTitle a', "url[] => @href")
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }
        $category_urls = $scraper->url;

        foreach ($category_urls as $category_url) {
            // 各サービスのURLを取得
            try {
                $url = $category_url;

                $scraper = new Diggin_Scraper();
                $scraper->process('div.contentsBoxCols3 p.contentsBoxTitle a', "url[] => @href")
                        ->scrape($url);
            } catch (Exception $e) {
                continue;
            }
            $service_urls = $scraper->url;

            // サービスのページからサービスIDを割り出す
            foreach ($service_urls as $service_url) {
                try {
                    $url = str_replace('/%20/service/kino', '', $service_url);

                    $scraper = new Diggin_Scraper();
                    $scraper->process('//div[@id="secondaryArea"]//a[starts-with(@href, "/cgi-bin")]', "serviceid[] => @href, Digits")
                            ->scrape($url);

                    $serviceid_list = array_merge($serviceid_list, $scraper->serviceid);
                } catch (Exception $e) {
                    continue;
                }
            }
        }

        sort($serviceid_list);


        $result = array();
        foreach ($serviceid_list as $serviceid) {
            try {
                $url = sprintf('http://www.au.kddi.com/cgi-bin/modellist/allList.cgi?ServiceID=%d', $serviceid);

                $scraper = new Diggin_Scraper();
                $scraper->process('h1', "service => TEXT")
                        ->process('//table//td', "models => TEXT")
                        ->scrape($url);
            } catch (Exception $e) {
                continue;
            }

            $row = array();

            preg_match('/「(.*)」対応機種/', $scraper->service, $match);

            $row['name']  = $match[1];
            $row['model'] = mb_split('\s*,\s*', $scraper->models);

            $result[] = $row;
        }

        return $result;
    }
}
