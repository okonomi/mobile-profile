<?php
require_once 'Diggin/Scraper.php';
require_once dirname(dirname(dirname(__FILE__))).'/Filter/Device.php';
require_once dirname(dirname(dirname(__FILE__))).'/Filter/String.php';
require_once dirname(dirname(dirname(__FILE__))).'/Filter/Size.php';


class Mobile_Profile_Collector_Docomo_Appli
{
    public function scrape()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/iappli/index.html';

            $star = new Diggin_Scraper_Process();
            $star->process('/td[not(@class) and @rowspan]', 'profile => "TEXT"')
                 ->process('/td[not(@class) and not(@rowspan)][1]/span', 'device => "RAW", DocomoDevice')
                 ->process('/td[not(@class) and not(@rowspan)][2]/span', 'size => "TEXT"')
                 ->process('/td[not(@class) and not(@rowspan)][3]/span', 'panel => "RAW", String, Size')
                 ->process('/td[not(@class) and not(@rowspan)][4]/span', 'canvas => "RAW", String, Size')
                 ->process('/td[not(@class) and not(@rowspan)][5]/span', 'heap => "TEXT"')
                 ->process('/td[not(@class) and not(@rowspan)][6]/span', 'heap_widget => "TEXT"')
                 ->process('/td[not(@class) and not(@rowspan)][7]/span', 'font => "TEXT", Size');
            $doja = new Diggin_Scraper_Process();
            $doja->process('/td[not(@class) and @rowspan]', 'profile => "TEXT"')
                 ->process('/td[not(@class) and not(@rowspan)][1]/span', 'device => "RAW", DocomoDevice')
                 ->process('/td[not(@class) and not(@rowspan)][2]/span', 'size => "TEXT"')
                 ->process('/td[not(@class) and not(@rowspan)][3]/span', 'panel => "RAW", String, Size')
                 ->process('/td[not(@class) and not(@rowspan)][4]/span', 'canvas => "RAW", String, Size')
                 ->process('/td[not(@class) and not(@rowspan)][5]/span', 'heap => "TEXT"')
                 ->process('/td[not(@class) and not(@rowspan)][6]/span', 'font => "TEXT", Size');
            $scraper = new Diggin_Scraper();
            $scraper->process('//div[@id="maincol"]/div[@class="boxArea"][1]//tr', array('star[]' => $star))
                    ->process('//div[@id="maincol"]/div[@class="boxArea"][2]//tr', array('doja[]' => $doja))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        $appli_profile = null;
        foreach ($scraper->results as $results) {
            foreach ($results as $profile) {
                if (isset($profile['profile'])) {
                    preg_match('/([a-z0-9\.\-]+)プロファイル$/i', $profile['profile'], $match);
                    $appli_profile = $match[1];
                }

                $row = array();

                $row['device'] = $profile['device']['device'];
                $row['model']  = $profile['device']['model'];

                $row['profile'] = $appli_profile;

                $tmp = array_pad(mb_split('/', $profile['size']), 2, null);
                $row['appli_size_jar']        = $tmp[0];
                $row['appli_size_scratchpad'] = $tmp[1];

                $row['panel_size']  = $profile['panel'];
                $row['canvas_size'] = $profile['canvas'];

                $tmp = array_pad(mb_split('/|／', $profile['heap']), 2, null);
                $row['heap_java']   = $tmp[0];
                $row['heap_native'] = $tmp[1];

                $row['heap_widget'] = $profile['heap_widget'];

                $row['font'] = $profile['font'];

                $result[] = $row;
            }
        }

        return $result;
    }
}
