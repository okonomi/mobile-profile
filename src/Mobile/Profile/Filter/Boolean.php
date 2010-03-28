<?php
require_once 'Zend/Filter/Interface.php';


class Mobile_Profile_Filter_Boolean implements Zend_Filter_Interface
{
    public function filter($value)
    {
        if ($value == '○' || $value == '△') {
            return 1;
        } elseif ($value == '×') {
            return 0;
        } else {
            return $value;
        }
    }
}
