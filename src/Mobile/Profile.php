<?php

class Mobile_Profile
{
    function Mobile_Profile()
    {
    }

    function get($carrier, $options = array())
    {
        $carrier = ucfirst(strtolower($carrier));

        $include_file = 'Mobile/Profile/'.$carrier.'.php';
        $class_name   = 'Mobile_Profile_'.$carrier;

        include_once $include_file;
        $module = new $class_name($options);

        $profile_info = $module->collect();

        return $profile_info;
    }
}
