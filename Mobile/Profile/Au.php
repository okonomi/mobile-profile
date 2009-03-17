<?php
require_once 'Mobile/Profile/Collector/Abstract.php';


class Mobile_Profile_Au extends Mobile_Profile_Abstract
{
    protected function _correctProfile()
    {
        $result = $this->_getScrape('deviceid');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['model']);

            // 機種名
            $info->setDeviceID($row['deviceid']);
            // モデル名
            $info->setModel($row['model']);
            // 世代
            if (preg_match('/^[A-Z]{2}2\w/', $row['deviceid'])) {
                if (preg_match('/^C\d+.*/', $row['model'])) {
                    $info->set('generation', 'cdmaOne');
                } else {
                    $info->set('generation', '1X');
                }
            } else {
                $info->set('generation', 'WIN');
            }
        }

        $result = $this->_getScrape('basic');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            // ブラウザの種類というかバージョンというか
            $info->set('browser', 'version', $row['browser_type']);
            // 表示領域
            $info->set('browser', 'screen', $row['browser_screen']);

            foreach ($row as $key => $val) {
                $info->set('basic', $key, $val);
            }
        }

        $result = $this->_getScrape('brew');
        foreach ($result as $row) {
            $row['model'] = preg_replace(
                array(
                    '/ \(カメラ無し\)/',
                    '/\s?カメラなしモデル/',
                ),
                'カメラ無し',
                $row['model']
            );

            $info =& $this->_getProfileInfo($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            foreach ($row as $key => $val) {
                $info->set('brew', $key, $val);
            }
        }

        $result = $this->_getScrape('java');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfo($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            foreach ($row as $key => $val) {
                $info->set('java', $key, $val);
            }
        }

        $result = $this->_getScrape('service');

        $service_names  = array();
        $service_models = array();
        foreach ($result as $row) {
            $service_names[] = $row['name'];

            foreach ($row['model'] as $model) {
                $service_models[$model][$row['name']] = true;
            }
        }

        foreach ($service_models as $model => $row) {
            $model = preg_replace(
                array(
                    '/\s?ケータイ\s?/',
                    '/([^\s]{1})(W)/',
                    '/\s?カメラなしモデル/',
                    '/’/',
                    '/\s\(/',
                ),
                array(
                    'ケータイ ',
                    '\\1 \\2',
                    'カメラ無し',
                    "'",
                    '(',
                ),
                $model
            );

            $info =& $this->_getProfileInfo($model, false);
            if (is_null($info)) {
                continue;
            }

            foreach ($row as $key => $val) {
                $info->set('service', $key, $val);
            }
        }
    }

    private function _getScrape($name)
    {
        $name = ucfirst(strtolower($name));

        $filename  = 'Mobile/Profile/Au/'.$name.'.php';
        $classname = 'Mobile_Profile_Au_'.$name;

        require_once $filename;
        $component = new $classname();
        $result = $component->scrape();

        return $result;
    }

    protected function &_getProfileInfo($value, $create = true)
    {
        $info = null;

        if (isset($this->info_data[$value])) {
            $info =& $this->info_data[$value];
            return $info;
        }

        if (!$create) {
            $_model = end(mb_split('\s+', preg_replace('/\s?I{2,3}/', '', $value)));
            $_model = str_replace('-', '\-', preg_quote($_model, '/'));
            $reg    = "/(^{$_model})|({$_model}$)/";

            foreach ($this->info_data as $key => $info) {
                if (preg_match($reg, $key)) {
                    return $info;
                }
            }
        }

        if ($create) {
            $info = new Mobile_Profile_Info();
            $this->info_data[$value] = & $info;
        } else {
            $info = null;
        }

        return $info;
    }
}
