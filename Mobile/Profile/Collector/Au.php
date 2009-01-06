<?php
require_once dirname(__FILE__) . '.php';


class Mobile_Profile_Collector_Au extends Mobile_Profile_Collector
{
    public function collect()
    {
        $result = $this->_getScrape('deviceid');
        foreach ($result as $row) {
            if ($row['deviceid'] === 'CA23') {
                $row['model'] = 'A5401CA/CA II';
            } elseif ($row['deviceid'] === 'TS25') {
                if (!preg_match('/カメラ/', $row['model'])) {
                    $row['model'] = 'A1304T/T II';
                }
            } else {
                $row['model'] = preg_replace('/\s?カメラなしモデル/', 'カメラ無し', $row['model']);
            }
            $info =& $this->_getProfileInfoByModel($row['model']);

            // 機種名
            $info->setDeviceID($row['deviceid']);
            // モデル名
            $info->setModel($row['model']);
        }

        $result = $this->_getScrape('basic');
        foreach ($result as $row) {
            $row['model'] = preg_replace('/\s?カメラなしモデル/', 'カメラ無し', $row['model']);

            $info =& $this->_getProfileInfoByModel($row['model'], false);
            if (is_null($info)) {
                continue;
            }

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

            $info =& $this->_getProfileInfoByModel($row['model'], false);
            if (is_null($info)) {
                continue;
            }

            foreach ($row as $key => $val) {
                $info->set('brew', $key, $val);
            }
        }

        $result = $this->_getScrape('java');
        foreach ($result as $row) {
            $info =& $this->_getProfileInfoByModel($row['model'], false);
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

            $info =& $this->_getProfileInfoByModel($model, false);
            if (is_null($info)) {
                continue;
            }

            foreach ($row as $key => $val) {
                $info->set('service', $key, $val);
            }
        }

        return $this->info_data;
    }

    private function _getScrape($name)
    {
        $name = ucfirst(strtolower($name));

        $filename  = dirname(__FILE__).'/Au/'.$name.'.php';
        $classname = 'Mobile_Profile_Collector_Au_'.$name;

        require_once $filename;
        $component = new $classname();
        $result = $component->scrape();

        return $result;
    }

    function &_getProfileInfoByModel($model, $create = true)
    {
        $info = null;

        foreach ($this->info_data as $device_id => $info) {
            if ($info->get('model') === $model) {
                return $info;
            }
        }

        if (!$create) {
            $_model = end(mb_split('\s+', preg_replace('/\s?I{2,3}/', '', $model)));
            foreach ($this->info_data as $device_id => $info) {
                if (preg_match('/(^'.$_model.')|('.$_model.'$)/', $info->get('model'))) {
                    return $info;
                }
            }
        }

        return $this->_getProfileInfo($model, $create);
    }
}
