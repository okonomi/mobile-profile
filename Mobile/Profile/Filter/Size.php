<?php
require_once 'Zend/Filter/Interface.php';


class Mobile_Profile_Filter_Size implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if (preg_match('/(\d+)×(\d+)/', $value, $match)) {
            return array(
                'width'  => $match[1],
                'height' => $match[2],
            );
        } else {
            return null;
        }
    }
}
