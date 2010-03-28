<?php
require_once 'Mobile/Profile/Au/Abstract.php';
require_once 'Diggin/Scraper.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Test.php';
require_once 'Mobile/Profile/Util.php';


class Mobile_Profile_Au_Service extends Mobile_Profile_Au_Abstract
{
    public function collect()
    {
        // サービスカテゴリのURLを取得
        try {
            $url = 'http://www.au.kddi.com/service/index.html';

            $scraper = new Diggin_Scraper();
            $scraper->process('p.contentsBoxTitle a', "url[] => @href")
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }

        $urls = array();
        foreach ($scraper->url as $url) {
            $urls[] = preg_replace('/^'.preg_quote('http://www.au.kddi.com', '/').'/', '', $url);
        }
        $pages = Mobile_Profile_Util::parallelRequest('www.au.kddi.com', $urls);


        $adapter = new Zend_Http_Client_Adapter_Test();
        $client  = new Zend_Http_Client();
        $client->setAdapter($adapter);
        Diggin_Scraper::setHttpClient($client);

        $urls = array();
        foreach ($pages as $page) {
            // 各サービスのURLを取得
            try {
                $adapter->setResponse($page);

                $scraper = new Diggin_Scraper();
                $scraper->process('div.contentsBoxCols3 p.contentsBoxTitle a', "url[] => @href")
                        ->scrape('http://www.au.kddi.com/');
            } catch (Exception $e) {
                continue;
            }

            foreach ($scraper->url as $url) {
                $urls[] = preg_replace('/^'.preg_quote('http://www.au.kddi.com', '/').'/', '', $url);
            }
        }
        $pages = Mobile_Profile_Util::parallelRequest('www.au.kddi.com', $urls);

        // サービスのページからサービスIDを割り出す
        $serviceid_list = array();
        foreach ($pages as $page) {
            try {
                $adapter->setResponse($page);

                $scraper = new Diggin_Scraper();
                $scraper->process('//div[@id="secondaryArea"]//a[starts-with(@href, "/cgi-bin")]',
                                  "serviceid[] => @href, Digits")
                        ->scrape('http://www.au.kddi.com/');

                $serviceid_list = array_merge($serviceid_list, $scraper->serviceid);
            } catch (Exception $e) {
                continue;
            }
        }

        $urls = array();
        foreach ($serviceid_list as $serviceid) {
            $urls[] = sprintf('/cgi-bin/modellist/allList.cgi?ServiceID=%d', $serviceid);
        }
        $pages = Mobile_Profile_Util::parallelRequest('www.au.kddi.com', $urls);


        $result = array();
        foreach ($pages as $page) {
            try {
                $adapter->setResponse($page);

                $scraper = new Diggin_Scraper();
                $scraper->process('h1', "service => TEXT")
                        ->process('//table//td', "models => TEXT")
                        ->scrape('http://www.au.kddi.com/');
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
