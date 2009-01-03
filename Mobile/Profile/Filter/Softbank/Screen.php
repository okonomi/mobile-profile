<?php
require_once 'Zend/Filter/Interface.php';


class Mobile_Profile_Filter_Softbank_Screen implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $screens = array();
        if (preg_match_all('/(([^:]+):)?(\d+) x (\d+)/', $value, $match)) {
            for ($i = 0; $i < count($match[0]); $i++) {
                $key = !empty($match[2][$i]) ? $match[2][$i] : $i;
                $screens[$key] = array(
                    'width'  => $match[3][$i],
                    'height' => $match[4][$i],
                );
            }
        } else {
            $screens[] = array();
        }

        return $screens;
    }
}
