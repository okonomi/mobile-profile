<?php
require_once 'Zend/Filter/Interface.php';


class Zend_Filter_String implements Zend_Filter_Interface
{
    public function filter($value)
    {
        return (string)$value;
    }
}
