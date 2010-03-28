<?php

class Mobile_Profile_Info
{
    protected $prop = array();


    public function __construct()
    {
    }

    public function set()
    {
        $keys = func_get_args();
        $value = array_pop($keys);
        $prop = & $this->prop;
        foreach ($keys as $key) {
            $prop = & $prop[$key];
        }

        $prop = $value;
    }

    public function setDeviceID($device_id)
    {
        $this->set('device', $device_id);
    }

    public function setModel($model)
    {
        $this->set('model', $model);
    }

    public function get($name)
    {
        if (isset($this->prop[$name])) {
            return $this->prop[$name];
        } else {
            return null;
        }
    }

    public function getPropArray()
    {
        return $this->prop;
    }

    public function setPropDef($prop)
    {
        foreach ($prop as $p) {
            $this->prop[$p] = null;
        }
    }
}
