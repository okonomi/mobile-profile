<?php
class Mobile_Profile_Formatter_Array
{
    function Mobile_Profile_Formatter_Array()
    {
    }

    function format($info)
    {
        $result = array();

        foreach ($info as $row) {
            $result[] = $row->getPropArray();
        }

        return $result;
    }
}