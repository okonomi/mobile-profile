<?php
require_once dirname(__FILE__).'/Info.php';


abstract class Mobile_Profile_Collector
{
    protected $info_data = array();

    protected $options = array();


    public function __construct($options = array())
    {
        $this->options = $options;
    }

    public function collect()
    {
        $this->_correctProfile();

        return $this->info_data;
    }

    abstract protected function _correctProfile();

    protected function &_getProfileInfo($name, $create = true)
    {
        $info = null;
        if (!isset($this->info_data[$name])) {
            if ($create) {
                $info = new Mobile_Profile_Info();
                $this->info_data[$name] = & $info;
            }
        } else {
            $info =& $this->info_data[$name];
        }

        return $info;
    }

    protected function &_getProfileInfoByModel($model, $create = true)
    {
        $info = null;

        foreach ($this->info_data as $device_id => $info) {
            if ($info->get('model') === $model) {
                return $info;
            }
        }

        return $this->_getProfileInfo($model, $create);
    }
}
