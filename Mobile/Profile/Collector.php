<?php
require_once 'HTTP/Request.php';
require_once 'Info.php';


class Mobile_Profile_Collector
{
    var $base_url;

    var $url_list = array();

    var $info_data = array ();

    var $options = array();


    function Mobile_Profile_Collector($options = array())
    {
        $this->options = $options;
    }

    function collect()
    {
        $this->_correctProfileInfo();

        return $this->info_data;
    }

    function _correctProfileInfo()
    {
        $request = new HTTP_Request();
        $request_options = array (
            'timeout'        => '10', // タイムアウトの秒数指定
            'allowRedirects' => true, // リダイレクトの許可設定(true/false)
            'maxRedirects'   => 3,    // リダイレクトの最大回数
        );

        foreach ($this->url_list as $type => $url_list) {
            if (!is_array($url_list)) {
                $url_list = array($url_list);
            }

            foreach ($url_list as $url) {
                $url = $this->base_url.$url;
                //echo "$url\n";

                $request->reset($url, $request_options);

                $response = $request->sendRequest();
                if (PEAR::isError($response)) {
                    echo $response->getMessage();
                    return false;
                }
                //echo "response\n";
                $content = $request->getResponseBody();

                $func = '_parseProfileInfo_'.ucfirst($type);

                $this->$func($content);
            }
        }
    }

    function &_getProfileInfo($name, $create = true)
    {
        $info = null;
        if (!isset($this->info_data[$name])) {
            if ($create) {
                $info = new Mobile_Profile_Info();
                $this->info_data[$name] = & $info;
            }
        } else {
            $info =& $this->info_data[$name];
        }

        return $info;
    }

    function &_getProfileInfoByModel($model, $create = true)
    {
        $info = null;

        foreach ($this->info_data as $device_id => $info) {
            if ($info->get('model') === $model) {
                return $info;
            }
        }

        return $this->_getProfileInfo($model, $create);
    }
}
