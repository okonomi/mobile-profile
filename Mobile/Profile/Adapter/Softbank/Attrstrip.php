<?php
require_once 'Diggin/Scraper/Adapter/Htmlscraping.php';


class Mobile_Profile_Adapter_Softbank_Attrstrip extends Diggin_Scraper_Adapter_Htmlscraping
{
    public function readData($response)
    {
        // tidyに改行されないように
        // レスポンスを小細工
        // これやっとかないと「xmlns=」で改行されて
        // Diggin_Scraper_Adapter_Htmlscrapingがxmlns属性を除去できなくなる

        // htmlのdir属性を削る
        $body = $response->getBody();
        $body = preg_replace('/(<html[^>]*)(\s+dir="[^"]+")/', '\1', $body);

        // 内容は符号化されていないことにする
        $headers = $response->getHeaders();
        if (isset($headers['Transfer-encoding'])) {
            unset($headers['Transfer-encoding']);
        }

        $_response = new Zend_Http_Response(
            $response->getStatus(),
            $headers,
            $body
        );

        return parent::readData($_response);
    }
}
