<?php
require_once dirname(__FILE__) . '.php';


class Mobile_Profile_Collector_Softbank extends Mobile_Profile_Collector
{
    public function collect()
    {
        $result = $this->_getScrape('httpheader');
        foreach ($result as $row) {
            if ($row['unique-id'] === '×' || is_null($row['unique-id'])) {
                $info =& $this->_getProfileInfo($row['device']);

                // 機種名
                $info->setDeviceID($row['device']);
                // モデル名
                $info->setModel($row['model']);

                // 色数
                $info->set('display', $row['color']);
            }
        }

        $result = $this->_getScrape('useragent');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfoByModel($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            $info->set('useragent', $row['useragent']);
        }

        $result = $this->_getScrape('service');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfoByModel($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            $info->set('flash', 'flash', $row['flash'] == '×' ? null : $row['flash']);
        }

        $result = $this->_getScrape('display');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfoByModel($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            $info->set('browser', 'screen', $row['browser_screen']);

            $info->set('browser', 'chars', $row['browser_chars']);

            $info->set('appli', 'screen', $row['appli_screen']);

            $info->set('appli', 'font', $row['appli_font']);

            $info->set('flash', 'display', $row['flash']);
        }

        $result = $this->_getScrape('appli');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfoByModel($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            unset($row['model']);

            foreach ($row as $key => $val) {
                $info->set('appli', $key, $val);
            }
        }

        $result = $this->_getScrape('function');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfoByModel($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            $info->set('display', 'lcd', $row['lcd']);
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
