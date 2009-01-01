<?php
require_once 'Diggin/Scraper.php';


class Mobile_Profile_Collector_Docomo_Useragent
{
    function scrape()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/useragent/index.html';

            $_Device = 'Mobile_Profile_Filter_Docomo_Device';

            $profile = new Diggin_Scraper_Process();
            $profile->process('/td[not(@scope) and @class="acenter middle"]', "series => TEXT")
                    ->process('/td[not(@scope) and not(@class="acenter middle")][1]/span', "device => RAW, $_Device")
                    ->process('/td[not(@scope)][count(img)=0][last()]', "ua => TEXT");
            $section = new Diggin_Scraper_Process();
            $section->process('div.titlept01 a', "version => TEXT")
                    ->process('//table/tr', array('profile[]' => $profile));
            $scraper = new Diggin_Scraper();
            $scraper->process('div.boxArea > div.wrap > div.section', array('section[]' => $section))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        $series = null;
        foreach ($scraper->section as $section) {
            preg_match('/iモード対応HTML([\d\.]+)/', $section['version'], $match);
            $html_version = $match[1];

            foreach ($section['profile'] as $profile) {
                if (isset($profile['series'])) {
                    $series = $profile['series'];
                }

                $row = array();

                $row['device']  = $profile['device']['device'];
                $row['model']   = $profile['device']['model'];
                $row['series']  = $series;
                $row['version'] = $html_version;

                $generation_list = array(
                    '1.0' => 'mova',
                    '2.0' => 'FOMA',
                );
                if (preg_match('/DoCoMo\/([\d\.]+)(\/| )[\w\d\+\-]+(\/|\()c(\d+)/i', $profile['ua'], $match)) {
                    $row['cache']      = (int)$match[4];
                    $row['generation'] = $generation_list[$match[1]];
                } else {
                    $row['cache']      = 5;
                    $row['generation'] = $generation_list['1.0'];
                }

                $result[] = $row;
            }
        }

        return $result;
    }
}
