<?php
require_once 'Zend/Http/Client/Adapter/Proxy.php';


class Mobile_Profile_Adapter_Softbank_Attrstrip extends Zend_Http_Client_Adapter_Proxy
{
    public function read()
    {
        $response = parent::read();

        // tidyで改行されないように属性をけずる
        $response = preg_replace('/dir="ltr"/', '', $response);

        return $response;
    }
}
