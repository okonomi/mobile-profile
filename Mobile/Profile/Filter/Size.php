<?php
require_once 'Zend/Filter/Interface.php';


class Mobile_Profile_Filter_Size implements Zend_Filter_Interface
{
    public function filter($value)
    {
        preg_match('/(\d+)×(\d+)/', $value, $match);
        $size = array(
            'width'  => $match[1],
            'height' => $match[2],
        );

        return $size;
    }
}
