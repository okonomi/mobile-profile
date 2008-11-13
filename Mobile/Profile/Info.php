<?php

class Mobile_Profile_Info
{
    var $prop = array();

    function Mobile_Profile_Info()
    {
    }

    function set()
    {
        $keys = func_get_args();
        $value = array_pop($keys);
        $prop = & $this->prop;
        foreach ($keys as $key) {
            $prop = & $prop[$key];
        }

        $prop = $value;
    }

    function setDeviceID($device_id)
    {
        $this->set('device', $device_id);
    }

    function setModel($model)
    {
        $this->set('model', $model);
    }

    function get($name)
    {
        if (isset($this->prop[$name])) {
            return $this->prop[$name];
        } else {
            return null;
        }
    }

    function getPropArray()
    {
        return $this->prop;
    }

    function setPropDef($prop)
    {
        foreach ($prop as $p) {
            $this->prop[$p] = null;
        }
    }
}
