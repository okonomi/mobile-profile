<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Docomo_Series
{
    public function scrape()
    {
        $url_base = 'http://www.nttdocomo.co.jp/';
        $url_list = array(
            'product/foma/style/index.html',
            'product/foma/prime/index.html',
            'product/foma/smart/index.html',
            'product/foma/pro/index.html',
        );

        $result = array();
        foreach ($url_list as $url) {
            try {
                $scraper = new Diggin_Scraper();
                $scraper->process('//head/title', 'series => "TEXT"')
                        ->process('div.section table tr td div.list-txt div.titlept02 h3 a', 'model[] => "TEXT"')
                        ->scrape($url_base.$url);
            } catch (Exception $e) {
                throw $e;
            }

            preg_match('/docomo (\w+) series/', $scraper->series, $match);
            $series = $match[1];

            foreach ($scraper->model as $model) {
                $model = mb_ereg_replace('&reg;', ' ', $model);
                $model = mb_ereg_replace('TM$', '', $model);

                $result[] = array(
                    'model'  => $model,
                    'series' => $series,
                );
            }
        }

        return $result;
    }
}
