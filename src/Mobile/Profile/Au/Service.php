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
        $pages = $this->_getServiceTaiouPages();

        $adapter = new Zend_Http_Client_Adapter_Test();
        $client  = new Zend_Http_Client();
        $client->setAdapter($adapter);
        Diggin_Scraper::setHttpClient($client);

        $result = array();
        foreach ($pages as $page) {
            try {
                $ret = $this->_getServiceModels($page);
            } catch (Exception $e) {
                continue;
            }

            for ($i = 0; $i < count($ret['names']); $i++) {
                $result[] = array(
                    'name'  => $ret['names'][$i],
                    'model' => $ret['models'][$i]['models'],
                );
            }
        }

        // 元に戻す(必要?)
        Diggin_Scraper::setHttpClient(new Zend_Http_Client());


        return $result;
    }

    protected function _getServiceTaiouPages()
    {
        try {
            $url = 'http://www.au.kddi.com/service/list.html';

            $scraper = new Diggin_Scraper();
            $scraper->process('ul.linkListHorizontal li a', "url[] => @href")
                    ->scrape($url);
        } catch (Diggin_Scraper_Exception $e) {
            throw $e;
        }

        $urls = array();
        foreach ($scraper->url as $url) {
            $url = preg_replace('!^http://www.au.kddi.com!', '', $url);
            $url = preg_replace('!index.html$!', 'taiou.html', $url);

            $urls[] = $url;
        }
        $pages = Mobile_Profile_Util::parallelRequest('www.au.kddi.com', $urls);


        return $pages;
    }

    protected function _getServiceModels($page)
    {
        try {
            Diggin_Scraper::getHttpClient()->getAdapter()->setResponse($page);


            $url = 'http://www.au.kddi.com/';

            $models = new Diggin_Scraper();
            $models->process('li', "models[] => TEXT");

            $scraper = new Diggin_Scraper();
            $scraper->process('h3', "names[] => TEXT")
                    ->process('//div[@class="contentsBox"]', array("models[]" => $models))
                    ->scrape($url);
        } catch (Diggin_Scraper_Exception $e) {
            throw $e;
        }

        return $scraper->getResults();
    }
}
