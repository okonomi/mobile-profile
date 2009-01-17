<?php
require_once 'Mobile/Profile/Formatter/Interface.php';


class Mobile_Profile_Formatter_Array implements Mobile_Profile_Formatter_Interface
{
    public function format($info)
    {
        $result = array();

        foreach ($info as $row) {
            $result[] = $row->getPropArray();
        }

        return $result;
    }
}
