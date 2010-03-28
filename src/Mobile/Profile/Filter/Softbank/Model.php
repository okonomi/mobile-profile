<?php
require_once 'Zend/Filter/Interface.php';


class Mobile_Profile_Filter_Softbank_Model implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $value = mb_ereg_replace('\?', 'II', $value);

        return $value;
    }
}
