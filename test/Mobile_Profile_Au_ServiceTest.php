<?php

require_once 'PHPUnit/Framework.php';

require_once 'Mobile/Profile/Au/Service.php';


class Mobile_Profile_Au_ServiceTest extends PHPUnit_Framework_TestCase
{
    public function testCorrect()
    {
        $correcter = new Mobile_Profile_Au_Service();
        var_dump($correcter->collect());
    }
}
