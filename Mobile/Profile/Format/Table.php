<?php
require_once 'Console/Table.php';

class Mobile_Profile_Format_Table
{
    function Mobile_Profile_Format_Table()
    {
    }

    function format($info)
    {
        $table = new Console_Table();
        $table->setHeaders($info['header']);

        foreach ($info['data'] as $row) {
            $props = $row->getPropArray();
            $addrow = array();
            foreach ($props as $key => $value) {
                if (is_array($value)) {
                } else {
                    $addrow[$key] = $value;
                }
            }
            $table->addRow($addrow);
/*             $table->addRow($row->getPropArray()); */
        }

        $string = $table->getTable();

        return $string;
    }
}