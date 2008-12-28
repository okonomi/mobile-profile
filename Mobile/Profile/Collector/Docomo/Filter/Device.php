<?php
require_once 'Zend/Filter/Interface.php';


class Zend_Filter_DocomoDevice implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $value = mb_ereg_replace('\n', '', (string)$value);
        $value = str_replace('&nbsp;', '', $value);

        preg_match('/([^（]+)(（(.*)）)?/iu', $value, $match);
        $device = array(
            'device' => $match[1],
            'model'  => isset($match[2]) ? $match[3] : $match[1],
        );

        $device['device'] = str_replace('FOMA ', '', $device['device']);
        $device['device'] = str_replace('&mu;', 'myu', $device['device']);
        $device['device'] = str_replace('-', '', $device['device']);

        $device['model'] = str_replace('&mu;', 'μ', $device['model']);

        return $device;
    }
}
