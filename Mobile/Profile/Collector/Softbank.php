<?php
require_once dirname(__FILE__) . '.php';


class Mobile_Profile_Collector_Softbank extends Mobile_Profile_Collector
{
    public function collect()
    {
        $result = $this->_getScrape('httpheader');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['device']);

            // 機種名
            $info->setDeviceID($row['device']);
            // モデル名
            $info->setModel($row['model']);

            // 色数
            $info->set('display', $row['color']);
        }

        $result = $this->_getScrape('service');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfoByModel($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            // Flashバージョン
            $info->set('flash', 'flash', $row['flash']);
        }


        return $this->info_data;
    }

    private function _getScrape($name)
    {
        $name = ucfirst(strtolower($name));

        $filename  = dirname(__FILE__).'/Softbank/'.$name.'.php';
        $classname = 'Mobile_Profile_Collector_Softbank_'.$name;

        require_once $filename;
        $component = new $classname();
        $result = $component->scrape();

        return $result;
    }
}
