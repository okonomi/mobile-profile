<?php
require_once 'Mobile/Profile/Info.php';


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

    protected function &_getProfileInfo($key, $create = true)
    {
        $info = null;
        if (!isset($this->info_data[$key])) {
            if ($create) {
                $info = new Mobile_Profile_Info();
                $this->info_data[$key] = & $info;
            }
        } else {
            $info =& $this->info_data[$key];
        }

        return $info;
    }

    protected function &_getProfileInfoByProp($prop, $value, $create = true)
    {
        $info = null;

        foreach ($this->info_data as $key => $info) {
            if ($info->get($prop) === $value) {
                return $info;
            }
        }

        return $this->_getProfileInfo($value, $create);
    }
}
