<?php

class Mobile_Profile
{
    var $profile_info;

    function Mobile_Profile()
    {
    }

    function &get($carrier, $options = array())
    {
        $carrier = ucfirst(strtolower($carrier));

        $include_file = 'Profile/Collector/'.$carrier.'.php';
        $class_name   = 'Mobile_Profile_Collector_'.$carrier;

        include_once dirname(__FILE__) . '/' . $include_file;
        $module = new $class_name($options);

        $this->profile_info = $module->collect();

        return $this;
    }

    function output($format)
    {
        $format = ucfirst(strtolower($format));

        $include_file = 'Profile/Formatter/'.$format.'.php';
        $class_name   = 'Mobile_Profile_Formatter_'.$format;

        include_once dirname(__FILE__) . '/' . $include_file;
        $module = new $class_name();

        $formated_content = $module->format($this->profile_info);

        return $formated_content;
    }
}
