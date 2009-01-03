<?php
require_once 'Zend/Http/Client/Adapter/Test.php';


class Mobile_Profile_Adapter_Softbank_Test extends Zend_Http_Client_Adapter_Test
{
    public function read()
    {
        $response = parent::read();

        // tidyで改行されないように属性をけずる
        $response = preg_replace('/<html lang="ja" xml:lang="ja" dir="ltr" /', '<html ', $response);

        return $response;
    }
}
