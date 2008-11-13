<?php
class Mobile_Profile_Format_Array
{
    function Mobile_Profile_Format_Array()
    {
    }

    function format($info)
    {
        $result = array();

        foreach ($info['data'] as $row) {
            $result[] = $row->getPropArray();
        }

        return $result;
    }
}