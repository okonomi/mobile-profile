<?php

class Mobile_Profile_Formatter_Csv
{
    function Mobile_Profile_Formatter_Csv()
    {
    }

    function format($info)
    {
        foreach ($info as $row) {
            $result .= $this->escapeCSV($row->getPropArray()) . "\r\n";
        }

        return $result;
    }

    function escapeCSV($row)
    {
        $ret = array();
        foreach ($row as $str) {
            $str = mb_ereg_replace('"', '\"\"', $str);
            $str = "\"$str\"";
            $ret[] = $str;
        }

        return implode(',', $ret);
    }
}
