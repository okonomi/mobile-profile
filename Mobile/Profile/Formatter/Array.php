<?php


class Mobile_Profile_Formatter_Array
{
    public function __construct()
    {
    }

    public function format($info)
    {
        $result = array();

        foreach ($info as $row) {
            $result[] = $row->getPropArray();
        }

        return $result;
    }
}
