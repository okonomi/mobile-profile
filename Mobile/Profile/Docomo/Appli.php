<?php
require_once 'Mobile/Profile/Docomo/Abstract.php';
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Docomo_Appli extends Mobile_Profile_Docomo_Abstract
{
    public function collect()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/iappli/index.html';

            $_Device = 'Mobile_Profile_Filter_Docomo_Device';
            $_Size   = 'Mobile_Profile_Filter_Size';
            $_String = 'Mobile_Profile_Filter_String';

            $star = new Diggin_Scraper();
            $star->process('/td[not(@class) and @rowspan]', "profile => TEXT")
                 ->process('/td[not(@class) and not(@rowspan)][1]/span', "device => RAW, $_Device")
                 ->process('/td[not(@class) and not(@rowspan)][2]/span', "size => TEXT")
                 ->process('/td[not(@class) and not(@rowspan)][3]/span', "panel => RAW, $_String, $_Size")
                 ->process('/td[not(@class) and not(@rowspan)][4]/span', "canvas => RAW, $_String, $_Size")
                 ->process('/td[not(@class) and not(@rowspan)][5]/span', "heap => TEXT")
                 ->process('/td[not(@class) and not(@rowspan)][6]/span', "heap_widget => TEXT")
                 ->process('/td[not(@class) and not(@rowspan)][7]/span', "font => TEXT, $_Size");
            $doja = new Diggin_Scraper();
            $doja->process('/td[not(@class) and @rowspan]', "profile => TEXT")
                 ->process('/td[not(@class) and not(@rowspan)][1]/span', "device => RAW, $_Device")
                 ->process('/td[not(@class) and not(@rowspan)][2]/span', "size => TEXT")
                 ->process('/td[not(@class) and not(@rowspan)][3]/span', "panel => RAW, $_String, $_Size")
                 ->process('/td[not(@class) and not(@rowspan)][4]/span', "canvas => RAW, $_String, $_Size")
                 ->process('/td[not(@class) and not(@rowspan)][5]/span', "heap => TEXT")
                 ->process('/td[not(@class) and not(@rowspan)][6]/span', "font => TEXT, $_Size");
            $scraper = new Diggin_Scraper();
            $scraper->process('//div[@id="maincol"]/div[@class="boxArea"][1]//tr', array('star[]' => $star))
                    ->process('//div[@id="maincol"]/div[@class="boxArea"][2]//tr', array('doja[]' => $doja))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        $appli_profile = null;
        foreach ($scraper->getResults() as $results) {
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
                $row['applisize'] = array(
                    'jar'        => $tmp[0],
                    'scratchpad' => $tmp[1],
                );

                $row['drawarea'] = array(
                    'panel'  => $profile['panel'],
                    'canvas' => $profile['canvas'],
                );

                $tmp = array_pad(mb_split('/|／', $profile['heap']), 2, null);
                $row['heap'] = array(
                    'java'   => $tmp[0],
                    'native' => $tmp[1],
                    'widget' => isset($profile['heap_widget']) ? $profile['heap_widget'] : null,
                );

                $row['font'] = $profile['font'];

                $result[] = $row;
            }
        }

        return $result;
    }
}
