<?php
require_once 'Diggin/Scraper.php';
require_once dirname(dirname(dirname(__FILE__))).'/Filter/Device.php';
require_once dirname(dirname(dirname(__FILE__))).'/Filter/Size.php';


class Mobile_Profile_Collector_Docomo_Flash
{
    function scrape()
    {
        try {
            $url = 'http://www.nttdocomo.co.jp/service/imode/make/content/spec/flash/index.html';

            $profile = new Diggin_Scraper_Process();
            $profile->process('/td[last()-6]/span', 'device => "RAW", DocomoDevice')
                    ->process('/td[last()-5]/span', 'browser => "TEXT", Size')
                    ->process('/td[last()-4]/span', 'display => "TEXT", Size')
                    ->process('/td[last()-3]/span', 'memory => "TEXT"')
                    ->process('/td[last()-2]/span', 'font => "RAW"')
                    ->process('/td[last()-2]/span/a', 'scalable_font => "TEXT"')
                    ->process('/td[last()-1]/img', 'pointing => "@alt"')
                    ->process('/td[last()-0]/span', 'inline => "TEXT"');
            $section = new Diggin_Scraper_Process();
            $section->process('div.titlept01 a', 'version => "TEXT"')
                    ->process('//table/tr[not(@class="brownLight acenter middle")]', array('profile[]' => $profile));
            $scraper = new Diggin_Scraper();
            $scraper->process('div.boxArea > div.wrap > div.section', array('section[]' => $section))
                    ->scrape($url);
        } catch (Exception $e) {
            throw $e;
        }


        $result = array();
        $flash_version = null;
        foreach ($scraper->section as $section) {
            $flash_version = $section['version'];

            foreach ($section['profile'] as $profile) {
                $row = array();

                $row['device']  = $profile['device']['device'];
                $row['model']   = $profile['device']['model'];
                $row['version'] = $flash_version;
                $row['browser'] = $profile['browser'];
                $row['display'] = $profile['display'];
                $row['memory']  = $profile['memory'];

                preg_match_all('/(\d+)×(\d+)/', (string)$profile['font'], $match);
                $fonts = array();
                for ($i = 0; $i < count($match[0]); $i++) {
                    $fonts[] = array(
                        'width'  => $match[1][$i],
                        'height' => $match[2][$i],
                    );
                }
                $scalable_font = (isset($profile['scalable_font']) && $profile['scalable_font'] == 1);

                $row['font']          = $fonts;
                $row['scalable_font'] = $scalable_font;

                $row['pointing'] = $profile['pointing'];
                $row['inline']   = $profile['inline'];

                $result[] = $row;
            }
        }

        return $result;
    }
}
