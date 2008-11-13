<?php

class Mobile_Profile_Format_Csv
{
    function Mobile_Profile_Format_Csv()
    {
    }

    function format($info)
    {
        $result = $this->escapeCSV($info['header']) . "\r\n";
        foreach ($info['data'] as $row) {
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
